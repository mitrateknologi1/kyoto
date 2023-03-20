<?php

namespace App\Http\Controllers\masterData;


use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Imports\AkunImport;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'role' => User::groupBy('role')->pluck('role')->toArray(),
            'status' => User::groupBy('status')->pluck('status')->toArray(),
        ];

        if ($request->ajax()) {
            $data = User::with('profile')->orderBy('created_at', 'DESC');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_profil', function ($row) {
                    if ($row->profile) {
                        return $row->profile->nama_lengkap;
                    } else {
                        return '<span class="badge badge-danger">Belum Ada Profil</span>';
                    }
                })
                ->addColumn('password_pengguna', function ($row) {
                    $output_password = str_repeat('*', strlen($row->password));
                    return $output_password;
                })
                ->addColumn('status_pengguna', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge badge-success">Aktif</span>';
                    } else {
                        return '<span class="badge badge-danger">Tidak Aktif</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '
                    <div class="row text-center justify-content-center">';
                    if (Auth::user()->id == 1) {
                        if ($row->id != 1) {
                            $actionBtn .= '<button id="btn-delete" onclick="hapus(' . $row->id . ')" class="btn btn-danger btn-sm mr-1 my-1" value="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>';
                        }
                        $actionBtn .= '
                        <a href="' . route('user.edit', $row->id) . '" id="btn-edit" class="btn btn-warning btn-sm mr-1 my-1" data-toggle="tooltip" data-placement="top" title="Ubah"><i class="fas fa-edit"></i></a>';
                    } else {
                        if ($row->id != 1) {
                            $actionBtn .= '
                            <a href="' . route('user.edit', $row->id) . '" id="btn-edit" class="btn btn-warning btn-sm mr-1 my-1" data-toggle="tooltip" data-placement="top" title="Ubah"><i class="fas fa-edit"></i></a>';
                            $actionBtn .= '<button id="btn-delete" onclick="hapus(' . $row->id . ')" class="btn btn-danger btn-sm mr-1 my-1" value="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>';
                        }
                    }
                    $actionBtn .= '</div>';
                    return $actionBtn;
                })
                ->filter(function ($query) use ($request) {
                    if ($request->search != '') {
                        $query->where(function ($query) use ($request) {
                            $query->whereHas('profile', function ($query) use ($request) {
                                $query->where("profiles.nama_lengkap", "LIKE", "%$request->search%");
                            });
                            $query->orWhere("users.username", "LIKE", "%$request->search%");
                        });
                    }

                    if (!empty($request->role)) {
                        $query->where('role', $request->role);
                    }

                    if (!empty($request->status)) {
                        $query->where('status', $request->status);
                    }
                })
                ->rawColumns(['nama_profil', 'password_pengguna', 'status_pengguna', 'action'])
                ->make(true);
        }
        return view('pages.masterData.user.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('pages.masterData.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username' => ['required', Rule::unique('users')->withoutTrashed()],
                'password' => 'required',
                'role' => 'required',
                'status' => 'required',

            ],
            [
                'username.required' => 'Nama Pengguna tidak boleh kosong',
                'username.unique' => 'Nama Pengguna sudah terdaftar',
                'password.required' => 'Kata Sandi tidak boleh kosong',
                'role.required' => 'Role tidak boleh kosong',
                'status.required' => 'Status tidak boleh kosong',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $data = [
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'status' => $request->status,
        ];

        User::create($data);

        return response()->json(['success' => 'Success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('pages.masterData.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username' => ['required', Rule::unique('users')->ignore($user->id)->withoutTrashed()],
                // 'password' => 'required',
                'role' => 'required',
                'status' => 'required',

            ],
            [
                'username.required' => 'Nama Pengguna tidak boleh kosong',
                'username.unique' => 'Nama Pengguna sudah terdaftar',
                // 'password.required' => 'Kata Sandi tidak boleh kosong',
                'role.required' => 'Role tidak boleh kosong',
                'status.required' => 'Status tidak boleh kosong',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        if ($request->password == '') {
            $password = $user->password;
        } else {
            $password = bcrypt($request->password);
        }

        $data = [
            'username' => $request->username,
            'password' => $password,
            'role' => $request->role,
            'status' => $request->status,
        ];

        $user->update($data);

        return response()->json(['success' => 'Success']);
        // return response()->json($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {

        $user->delete();
        $user->profile()->delete();
        return response()->json([
            'res' => 'success'
        ]);
    }
}

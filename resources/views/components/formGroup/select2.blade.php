<div class="form-group">
    <label for="">{{$label}}</label>
    <select name="{{$name}}" class="form-control form-control {{$class}}" id="{{$id}}">
        {{$options}}
    </select>
    <span class="text-danger error-text {{$name}}-error"></span>
</div>

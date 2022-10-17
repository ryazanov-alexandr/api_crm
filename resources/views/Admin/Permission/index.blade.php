<!-- Page header -->
<section class="content-header">
</section>
<!-- /page header -->

<!-- Content area -->
<div class="content">
    <!-- Hover rows -->
    <div class="card">
        <form class="table-responsive"  enctype="multipart/form-data" method="post" action="{{route('permissions.store')}}">

            @csrf
            @if($perms)
                <table class="table table-hover">
                    <thead class="thead-dark">
                    <th style="font-size: 22px;">{{__('Разрешения')}}</th>
                    @if(!$roles->isEmpty())
                        @foreach($roles as $item)
                            <th  style="font-size: 22px;">{{ $item->title}}</th>
                        @endforeach
                    @endif
                    </thead>
                    <tbody>
                    @if(!$perms->isEmpty())
                        @foreach($perms as $val)
                            <tr>
                                <td style="font-size: 20px;"><strong>{{ $val->title }}</strong></td>
                                @foreach($roles as $role)
                                    <td>
                                        <label class="checkbox-label">
                                            @if($role->hasPermission($val->alias))
                                                <input checked name="{{ $role->id }}[]" type="checkbox"
                                                       class="checkbox-input" value="{{ $val->id }}">
                                            @else
                                                <input class="checkbox-input" name="{{ $role->id }}[]" type="checkbox"
                                                       value="{{ $val->id }}">
                                            @endif
                                            <span></span>
                                        </label>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
                <div>
                </div>

                <button style="margin: 15px;" type="submit" class="btn btn-success">{{__('Сохранить')}}</button>

        </form>
        @endif
    </div>
</div>
<!-- /hover rows -->

</div>
<!-- /content area -->

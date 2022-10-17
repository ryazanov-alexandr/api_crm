<!-- Page header -->
<section class="content-header">
    <h1 style="margin-bottom:15px;">{{__('Роли')}}</h1>
    <a href="{{route('roles.create')}}" class="btn btn-success">{{ __('Создать') }}</a>

</section>
<!-- /page header -->

<!-- Content area -->
<div class="content">
    <!-- Hover rows -->
    <div class="card">
        <div class="table-responsive">
            @if($roles)
                <table class="table table-hover">
                    <thead class="thead-dark">
                    <tr>
                        <th>{{ __('№') }}</th>
                        <th>{{ __('Название') }}</th>
{{--                        <th>{{ __('Системное название') }}</th>--}}
                        <th>{{ __('Действие') }}</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($roles as $i=>$role)
                        <tr>
                            <td>{{$i+1}}</td>
                            <td>{{$role->title}}</td>
{{--                            <td>{{$role->alias}}</td>--}}
                            <td>
                                <div class="row">
                                <a href="{{route('roles.edit',['role'=>$role->id])}}"
                                   class="btn btn-primary btn-labeled">{{ __('Изменить') }}
                                </a>


                                <form method="post"  action="{{route('roles.delete',['role'=>$role->id])}}">
                                    @csrf
                                    @method('DELETE')
                                    <button  type="submit" class="btn btn-danger">{{ __('Удалить') }}
                                    </button>
                                </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    <div style="display:none">
                        <form method="post" id="contact-applications-delete" action="">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>

                    </tbody>
                </table>
            @endif
        </div>
    </div>
    <!-- /hover rows -->

</div>
<!-- /content area -->

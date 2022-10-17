<!-- Page header -->
<section class="content-header">
    <h1>Добавление роли</h1>
</section>
<!-- /page header -->


<!-- Content area -->
<div class="content">

    <!-- Input group addons -->
    <div class="box card">
        <form role="form" enctype="multipart/form-data" method="post" action="{{ route('roles.store') }}">

            @csrf

            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @csrf
                <fieldset class="mb-3">
                    <legend class="">{{__('Общая информация')}}</legend>

                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">{{__('Название')}}<span
                                class="text-danger">*</span></label>
                        <div class="col-lg-10">
                            <div class="input-group">
                                <input type="text" name="Название" required class="form-control"
                                       value="{{old('title')}}"
                                       placeholder="{{__('Название')}}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">{{__('Системное название')}}<span
                                class="text-danger">*</span></label>
                        <div class="col-lg-10">
                            <div class="input-group">
                                <input type="text" name="Системное название" class="form-control"
                                       value="{{old('alias')}}"
                                       placeholder="{{__('Системное название')}}">
                            </div>
                        </div>
                    </div>


                </fieldset>
                <button type="submit" class="btn btn-success">{{__('Сохранить')}}</button>


            </div>
        </form>
    </div>
    <!-- /input group addons -->

</div>

<!-- /content area -->

@extends('layouts.app')

@section('titulo')
    Home
@endsection

@section('contenedor')
    <div class="container-fluid">
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Select2 (Default Theme)</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
              <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
            </div>
          </div>
        <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
                <form action="" method="post">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Nombre</label>
                                <input type="text" name="nombre" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Precio</label>
                                <input type="text" name="precio" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Cantidad</label>
                                <input type="text" name="cantidad" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success float-right">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.row -->

         
          </div>
      <!-- /.card-body -->
    </div>
    </div>
@endsection

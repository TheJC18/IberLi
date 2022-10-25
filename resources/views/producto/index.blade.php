@extends('layouts.app')

@section('titulo')
    Home
@endsection

@section('contenedor')
    <div class="container-fluid">
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Productos</h3>
          </div>
        <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($producto as $value)
                            <tr>
                                <td>{{$value->id}}</td>
                                <td>{{$value->nombre}}</td>
                                <td>{{$value->precio}}</td>
                                <td>{{$value->cantidad}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.row -->

         
          </div>
        <!-- /.card-body -->
        </div>
    </div>
@endsection

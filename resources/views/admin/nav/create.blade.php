@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加分类</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('admin.nav.store')}}" method="post">
                @include('admin.nav._form')
            </form>
        </div>
    </div>
@endsection
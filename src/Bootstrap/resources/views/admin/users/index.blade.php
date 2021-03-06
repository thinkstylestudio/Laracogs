@extends('dashboard')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <form id="" class="pull-right raw-margin-top-24 raw-margin-left-24" method="post" action="/admin/users/search">
                {!! csrf_field() !!}
                <input class="form-control" name="search" placeholder="Search">
            </form>
            <a class="btn btn-default pull-right raw-margin-top-24" href="{{ url('admin/users/invite') }}">Invite New User</a>
            <h1>User Admin</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped raw-margin-top-24">

                <thead>
                    <th>Email</th>
                    <th class="text-right">Actions</th>
                </thead>
                <tbody>
                    @foreach($users as $user)

                        @if ($user->id !== Auth::id())
                            <tr>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <a class="btn btn-danger btn-xs raw-margin-left-16 pull-right" href="{{ url('admin/users/'.$user->id.'/delete') }}" onclick="return confirm('Are you sure you want to delete this user?')"><span class="fa fa-edit"></span> Delete</a>
                                    <a class="btn btn-warning btn-xs pull-right" href="{{ url('admin/users/'.$user->id.'/edit') }}"><span class="fa fa-edit"></span> Edit</a>
                                </td>
                            </tr>
                        @endif

                    @endforeach

                </tbody>

            </table>
        </div>
    </div>

@stop

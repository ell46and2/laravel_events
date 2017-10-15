<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-default">
      <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Brand</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Users<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">View</a></li>
                <li><a href="#">Approval List <span class="badge">4</span></a></li>
                <li><a href="#">Blocked List</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Events <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">View</a></li>
                <li><a href="#">Create</a></li>
              </ul>
            </li>
            <li><a href="">Logs</a></li>
            <li>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                    Logout
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>

    <div class="container">
        <div class="x_content">

          <div class="table-responsive">
            <table class="table table-striped jambo_table bulk_action">
              <thead>
                <tr class="headings">
                  <th class="column-title">Name </th>
                  <th class="column-title">Email </th>
                  <th class="column-title no-link last text-center actions-column users-actions-column"><span class="text-center">Actions</span></th>
                </tr>
              </thead>

              <tbody>
               @foreach ($users as $key=>$user)
                <tr class="{{$key%2 != 0 ?'even' : 'odd'}} pointer">
                  <td class=" ">{{$user->first_name}} {{$user->last_name}}</td>
                  <td class=" ">{{$user->email}}</td>
                  <td class="text-center">
                    <a href="" class="btn btn-success">Edit</a>
                    <a href="" class="btn btn-primary">Block</a>
                    <a href="" class="btn btn-danger">Delete</a>
                </td>
                </tr>
              @endforeach
              @foreach ($users as $user)
                <tr class="{{$key%2 != 0 ?'even' : 'odd'}} pointer">
                  <td class=" ">{{$user->first_name}} {{$user->last_name}}</td>
                  <td class=" ">{{$user->email}}</td>
                  <td class="text-center">
                    <a href="" class="btn btn-success">Edit</a>
                    <a href="" class="btn btn-primary">Block</a>
                    <a href="" class="btn btn-danger">Delete</a>
                </td>
                </tr>
              @endforeach  
               
              </tbody>
            </table>
          </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link href="/css/app.css" rel="stylesheet">
        
        <style>
            .avatar {
                vertical-align: middle;
                width: 50px;
                height: 50px;
                border-radius: 50%;
            }

            .symbol {
                width: 100px;
                height: 100px;
                background-color: #000;
                border-radius: 20px;
                position:relative;
            }

        </style>

        
    </head>
    <body>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card card-default">
                        <div class="card-header"><img src="https://www.7shifts.com/images/media-kit/logo-black.png" class="logo">7Shifts Calculator</div>

                        <div class="card-body">
                            
                            <table class="table">
                                <th></th>
                                <th>First Name</th>
                                <th>Total Horas</th>
                                <th>Total Overtime</th>
                                <th>Actions</th>
                                <tbody>
                                
                                @foreach($users as $user)
                                    <tr>
                                        <td><img class="avatar" src="{{ $user['photo'] }}"></td>
                                        <td>{{ $user['firstName'] . ' ' . $user['lastName'] }}</td>
                                        <td>{{ $user['detail']['total_time'] }}</td>
                                        <td>{{ $user['detail']['total_overtime']}}</td>
                                        <td>
                                            <button class="btn btn-primary" 
                                                    type="button" 
                                                    data-toggle="collapse" 
                                                    data-target="#{{$user['id']}}" 
                                                    aria-expanded="false" 
                                                    aria-controls="{{$user['id']}}">
                                            more
                                          </button>
                                        </td>
                                    </tr>

                                    <tr class="collapse" id="{{$user['id']}}">
                                        <td class="card card-body" colspan="4">
                                            <ul>
                                                <b>{{ $user['location']['address'] }}</b>
                                                <li>Total work time: {{$user['detail']['total_time']}}</li>
                                                <li>Total Weekly Overtime: {{$user['detail']['week_overtime']}}</li>
                                                <li>Total Daily Overtime time: {{$user['detail']['day_overtime']}}</li>
                                                <li>Total Salary: U$ {{$user['detail']['total_paid']}}</li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

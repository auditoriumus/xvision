<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Система учета номерных знаков XVISION 1.0</title>
</head>
<style>
    * {
        box-sizing: border-box;
    }

    @import url('https://fonts.googleapis.com/css?family=Rubik:400,500&display=swap');
    body {
        font-family: 'Rubik', sans-serif;
    }

    .container {
        display: flex;
        height: 100vh;
    }

    .left {
        overflow: hidden;
        display: flex;
        flex-wrap: wrap;
        flex-direction: column;
        justify-content: center;
        animation-name: left;
        animation-duration: 1s;
        animation-fill-mode: both;
        animation-delay: 1s;
    }

    .right {
        flex: 1;
        background-color: black;
        transition: 1s;
        background-image: url("{{asset('/fon2.png')}}");
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }

    .header > h2 {
        margin: 0;
        color: #4f46a5;
    }

    .header > h4 {
        margin-top: 10px;
        font-weight: normal;
        font-size: 15px;
        color: rgba(0, 0, 0, .4);
    }

    .form {
        max-width: 80%;
        display: flex;
        flex-direction: column;
    }

    .form > p {
        text-align: right;
    }

    .form > p > a {
        color: #000;
        font-size: 14px;
    }

    .form-field {
        height: 46px;
        padding: 0 16px;
        border: 2px solid #ddd;
        border-radius: 4px;
        font-family: 'Rubik', sans-serif;
        outline: 0;
        transition: .2s;
        margin-top: 20px;
    }

    .form-field:focus {
        border-color: #0f7ef1;
    }

    .form > button, .form > label {
        padding: 12px 10px;
        border: 0;
        background: linear-gradient(to right, #de48b5 0%, #0097ff 100%);
        border-radius: 3px;
        margin-top: 10px;
        color: #fff;
        letter-spacing: 1px;
        font-family: 'Rubik', sans-serif;
    }

    .animation {
        animation-name: move;
        animation-duration: .4s;
        animation-fill-mode: both;
        animation-delay: 2s;
    }

    .a1 {
        animation-delay: 2s;
    }

    .a2 {
        animation-delay: 2.1s;
    }

    .a3 {
        animation-delay: 2.2s;
    }

    .a4 {
        animation-delay: 2.3s;
    }

    .a5 {
        animation-delay: 2.4s;
    }

    .a6 {
        animation-delay: 2.5s;
    }

    @keyframes move {
        0% {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-40px);
        }

        100% {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
    }

    @keyframes left {
        0% {
            opacity: 0;
            width: 0;
        }

        100% {
            opacity: 1;
            padding: 20px 40px;
            width: 440px;
        }
    }
</style>
<body>
@if(isset($number) && !empty($number))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Автомобиль с номером <b>{{$number}}</b> заезжает на стоянку.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="container">
    <div class="left">
        <div class="header">
            <h2 class="animation a1">Прикрепите видео и нажмите отправить</h2>
            <h4 class="animation a2">Для более корректной работы, выберите небольшой ролик, имитирующий видео, снятое
                камерой, установленной возле шлагбаума и прямо смотрящее на номер машины, на котором отчетливо виден
                номер автомобиля.</h4>
        </div>
        <form class="form" method="post" action="{{route('add_video_file')}}" enctype="multipart/form-data">
            @csrf
            <label for="file" id="label" class="animation a6">Прикрепить</label>
            <input name="video" type="file" id="file" hidden placeholder="Прикрепить">
            <button type="submit" class="animation a6">Отправить</button>
        </form>
        @if(isset($journal))
            <div class="left animation a1 mt-3">
                <h6>Автомобили на стоянке:</h6>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Номер</th>
                        <th scope="col">Время</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($journal as $item)
                        <tr>
                            <th scope="row">{{$item->number}}</th>
                            <th>{{$item->created_at->format('H:i d/m')}}</th>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
    </div>
    <div class="right"></div>
</div>
<script>
    document.getElementById('file').addEventListener('change', function () {
        if (this.value) {
            document.getElementById('label').innerText = this.value
            console.log(this.value)
        } else { // Если после выбранного тыкнули еще раз, но дальше cancel
            console.log("Файл не выбран");
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

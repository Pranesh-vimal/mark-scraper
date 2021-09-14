<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <title>{{ config('app.name', 'Laravel') }}</title>
</head>

<body class="antialiased">
    <div class="my-10 text-center">
        <form action="{{ route('upload') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="my-2">
                <label for="file" class="p-2">File :</label>
                <input type="file" name="file" id="file" class="p-2" />
                @if ($errors->has('file'))
                <div class="my-2 text-red-500">{{ $errors->first('file') }}</div>
                @endif
            </div>
            <div class="my-2">
                <button type="submit" class="p-2 bg-green-400 rounded-md text-white">Upload</button>
            </div>
        </form>
    </div>
</body>

</html>
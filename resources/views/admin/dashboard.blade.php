<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <title>Dashboard</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        /* Float four columns side by side */
        .column {
            float: left;
            width: 25%;
            padding: 0 10px;
        }

        /* Remove extra left and right margins, due to padding */
        .row {
            margin: 0 -5px;
        }

        /* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        /* Responsive columns */
        @media screen and (max-width: 600px) {
            .column {
                width: 100%;
                display: block;
                margin-bottom: 20px;
            }
        }

        /* Style the counter cards */
        .card {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            padding: 16px;
            text-align: center;
            background-color: #f1f1f1;
        }

        button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #de3714;
            color: white;
            border: none;
            cursor: pointer;
        }

        a {
            text-decoration: none;
        }
    </style>
</head>

<body>
    <tr>

        <h1>Admin Dashboard</h1>

        <form method="POST" action={{ route('admin.logout') }}>
            @csrf
            <button type="submit">Logout</button>
        </form>
    </tr>

    <div class="row">
        <div class="column">
            <a href="{{ route('doctor.index') }}">
            <div class="card">
                <h3>Doctors</h3>
                <p>{{ $doctorCount }}</p>
            </div>
            </a>
        </div>

        <div class="column">
            <div class="card">
                <h3>Patients</h3>
                <p>{{ $patientCount }}</p>
            </div>
        </div>



</body>

</html>
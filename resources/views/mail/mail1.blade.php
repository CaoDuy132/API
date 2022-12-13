<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div style="width:60%;margin:0px auto">
        <h1 style="padding-top:10px;padding-bottom:10px;background:black;color:white;font-weight:bolder;font-size:23px;border-top-left-radius:12px;border-top-right-radius: 12px;text-align:center">Thông tin tài khoản</h1>
        <div style="width:60%;margin:0px auto;text-align:center" id="container">
            <p style="font-weight:bold;font-size:20px">Mật khẩu tài khoản : </p> <span style="font-size:16px">{{$details['password']}}</span>
            <p style="font-weight:bold;font-size:20px">Thông báo : </p> <span style="font-size:16px">{{$details['thongbao']}}</span>
        </div>
    </div>

</body>
</html>

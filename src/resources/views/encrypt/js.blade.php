<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Document</title>
</head>
<body>
<form action="" method="post">
    <textarea name="string" cols="180" rows="5"></textarea>
    <br />
    <input type="submit" />
</form>
@if(isset($data))<textarea cols="180" rows="20">{{$data}}</textarea>@endif
</body>
</html>
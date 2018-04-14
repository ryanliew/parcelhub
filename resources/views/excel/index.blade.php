<!DOCTYPE html>
<html>
<body>

<form action="/excel/store" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="hidden" name="_token" value="{{csrf_token()}}"/>
    <input type="file" name="file" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
</form>

</body>
</html>
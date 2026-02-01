<!DOCTYPE html>
<html lang="en">
<head>
    <title>BookBus</title>
</head>
<body>
    <h1>Welcome to BookBus Laravel Project</h1>
    <p>This is the J1 Livrable setup.</p>

    <fieldset>
        <legend>test form</legend>

        <form action="/register" method="POST">
            @csrf
            <input type="text" name="name" placeholder="enter your name...">
            <input type="email" name="email" placeholder="enter your email...">
            <input type="password" name="password" placeholder="enter your name...">

            <input type="submit">
        </form>
    </fieldset>
</body>
</html>
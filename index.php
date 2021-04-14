<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <form action="dl.php" method="post" class="myForm">
        <textarea name="link"></textarea>
        <input type="text" name="f" />
        <button id="Sub">Download</button>
    </form>
    <div class="result" style="background-color:blue;width:100px;height:300px;">
        
    </div>
<script type="text/javascript" charset="utf-8">
            $("#Sub").click(function(e){
        $.ajax({
            type: "POST",
            url: "dl.php",
            data: $(".myForm").serialize(),
            success: function(response){
                $(".result").html(response);
            }
        });
return false;
    })
        </script>
</body>
</html>

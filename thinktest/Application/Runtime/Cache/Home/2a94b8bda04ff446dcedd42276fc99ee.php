<?php if (!defined('THINK_PATH')) exit();?><html>
	<head>
		<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
	</head>
	<body>
		<form  id='ppp' >
			<input type="file" name="photo" />
			<input type="submit" value="提交" onclick="test();">
		</form>
		<script type="text/javascript">
			function test(){
				 var form = new FormData(document.getElementById("ppp"));
				 $.ajax({
	                url:"/thinktest/index.php?s=/Home/Index/upload",
	                type:"post",
	                data:form,
	                processData:false,
	                contentType:false,
	                success:function(data){
	                   console.log(data);
	                },
	                error:function(e){
	                    
	                }
	            });
			}
		</script>
	</body>

</html>
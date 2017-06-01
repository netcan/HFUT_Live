<?php session_start(); ?>
<html>
	<head>
		<meta charset="utf-8">
		<title>校内直播平台 By Netcan</title>
		<link rel="stylesheet" href="node_modules/semantic-ui/dist/semantic.min.css" type="text/css" media="all" />
	</head>
	<body style="background-color: #CCC4C2">

		<div class="ui white massive main menu">
			<div class="ui container">
				<a href="./" class="header item">
					HFUT直播平台
				</a>
				<div class="right menu">
					<a class="item" href="help.html">
					    直播教程
					</a>
					<a class="item">
						平台在线人数：<i class="home icon" id="roomHolder"></i> / <i class="user icon" id="onlineUser"></i>
					</a>
				</div>
			</div>
		</div>

		<div class="ui container">
			<div class="ui segment">
				重大更新：
				<ul>
					<li>大厅即可接收保存所有房间信息，进入对应房间即可看到收到的历史信息。</li>
				</ul>
				目前计划：
				<ul>
					<li>实现视频内弹幕</li>
				</ul>
			</div>
			<div id="room" class ="ui four column grid">
			</div>
		</div>

		<script src="node_modules/jquery/dist/jquery.min.js"></script>
		<script src="node_modules/semantic-ui/dist/semantic.min.js"></script>
		<script src="node_modules/socket.io-client/dist/socket.io.js"></script>

		<script>
			var api = "api.php";
			function getRoom() {
				$.ajax({
					url: api + "?getRoom",
					type: "GET",
					dataType: "json",
					success: function(a) {
						var content = "";
						for(var i = 0; i < a.length; ++i)
							content += "<div class='column'>"
								+ "<div class='ui card'>" +
								"<a href='player.php?room="+ a[i] + "'>"+
								"<img class='ui image' width=260.75 height=195.56 src='live/"+ a[i] + ".jpg?" + new Date().getTime() + "'  title='"+ a[i] +"'>" +
								"</a>" +
								'<div class="content">' +
								'<a class="header" href=player.php?room=' + a[i] + '>'+ a[i] +'</a>' +
								'<i class="unhide icon"></i><span id="' + a[i] + '">0</span>'  +
								'</div>' +
								"</div>" +
								"</div>";

						$('#room').html(content);
						$('#roomHolder').text(a.length);
						// console.log(content);
					}
				});
			}
			getRoom();
			setInterval(getRoom, 20000); // 20秒刷新一次

			var socket = io("<?php echo $_SERVER['SERVER_ADDR'] . ':2120'?>");

			socket.on('connect', function(){socket.emit('login', "<?php echo session_id(); ?>");});

			socket.on('new_msg', function(msg){
				// alert(msg);
				msg = JSON.parse(msg);
				if(localStorage.getItem(msg["channel"]))
					localStorage.setItem(msg["channel"], localStorage.getItem(msg["channel"]) + '<p>' + msg.datetime + '<br>' + msg.content); // 保存聊天记录
				else
					localStorage.setItem(msg["channel"], '<p>' + msg.datetime + '<br>' + msg.content); // 保存聊天记录
			});

			socket.on('update_online_count', function(online_stat){
				online_stat = JSON.parse(online_stat);
				$('#onlineUser').text(online_stat.online_count_now)
				// console.log(online_stat);
			});

			socket.on('update_room_online_count', function(online_room_stat){
				online_room_stat = JSON.parse(online_room_stat);
				for(var room in online_room_stat) {
					console.log(online_room_stat[room]);
					$('#' + room).text(online_room_stat[room]);
				}
				// $('#roomOnlineUser').text(online_stat[room]);
			});

		</script>
	</body>

</html>

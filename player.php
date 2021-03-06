<?php session_start(); ?>
<html>
	<head>
		<meta charset="utf-8">
		<title>校内直播平台 By Netcan</title>
		<link rel="stylesheet" href="node_modules/semantic-ui/dist/semantic.min.css" type="text/css" media="all" />
		<link href="node_modules/video.js/dist/video-js.min.css" rel="stylesheet">
		<link href="css/style.css" rel="stylesheet">
		<link href="css/barrager.css" rel="stylesheet">
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
						平台在线人数：<i class="user icon" id="onlineUser"></i>
					</a>
				</div>
			</div>
		</div>

		<div class="ui container">
		<h2>当前房间（<i class="unhide icon"></i><span id="roomOnlineUser">0</span>）：<span id="curRoom"></span></h2>
			<div class="ui grid">
				<div class="twelve wide column">
					<div class="row">
						<div id="danmu">
							<video id=player class="video-js vjs-default-skin" controls data-setup='{"fluid": true, "aspectRatio": "16:9"}'>
								<source src="" type="application/x-mpegURL">
							</video>
						</div>
					</div>

					<h4 class="ui horizontal divider header"><i class="send icon"></i>弹幕发射区</h4>
					<div class="ui form">
						<div class="fields">
							<div class="three wide field">
								<input type="text" name="name" id="userName" placeholder="昵称">
							</div>
							<div class="thirteen wide field">
								<input type="text" name="content" id="danmuContent" placeholder="发送弹幕内容">
							</div>
						</div>
					</div>

				</div>
				<div class="four wide column">
					<div class="ui segment">
						<div class="ui toggle checkbox" id="msgScroll">
							<input type="checkbox" tabindex="0" class="hidden">
							<label>自动滚动</label>
						</div>
						<div class="ui toggle checkbox" id="danmuOn">
							<input type="checkbox" tabindex="0" class="hidden">
							<label>弹幕</label>
						</div>
						<div class="outmaxh" id="message">
						</div>
					</div>
				</div>
			</div>

		</div>

		<script src="node_modules/jquery/dist/jquery.min.js"></script>
		<script src="node_modules/semantic-ui/dist/semantic.min.js"></script>
		<script src="node_modules/socket.io-client/dist/socket.io.js"></script>
		<script src="node_modules/video.js/dist/video.min.js"></script>
		<script src="node_modules/videojs-contrib-hls/dist/videojs-contrib-hls.min.js"></script>
		<script src="js/jquery.barrager.js"></script>

		<script>
			function html_encode(str)
			{
			  var s = "";
			  if (str.length == 0) return "";
			  s = str.replace(/&/g, "&gt;");
			  s = s.replace(/</g, "&lt;");
			  s = s.replace(/>/g, "&gt;");
			  s = s.replace(/ /g, "&nbsp;");
			  s = s.replace(/\'/g, "&#39;");
			  s = s.replace(/\"/g, "&quot;");
			  s = s.replace(/\n/g, "<br>");
			  return s;
			}

			// 登录
			var danmuUrl = "<?php echo $_SERVER['SERVER_ADDR'] . ':2120'?>";
			var socket = io(danmuUrl);
			var api = "api.php";
			var room = "<?php echo $_GET['room'] ?>";

			socket.on('connect', function(){socket.emit('login', "<?php echo session_id(); ?>");});
			// 一个人只去一个房间
			socket.on('connect', function(){socket.emit('enterRoom', "<?php echo session_id(); ?>", room);});

			// 弹幕事件
			$('#msgScroll').checkbox("check");
			$('#danmuOn').checkbox("check");

			if(localStorage.getItem(room))  // 首次加载
				$('#message').html(localStorage.getItem(room));

			socket.on('new_msg', function(msg){
				// alert(msg);
				msg = JSON.parse(msg);
				if(msg['channel'] == room) {
					$('#message').append(
							'<p>' + msg.datetime + '<br>' + msg.content
					);
					localStorage.setItem(room, $('#message').html()); // 保存聊天记录

					if($('#danmuOn').checkbox("is checked")) {
						var item={
							img:'imgs/anonymous.png', //图片
							info:  html_encode(msg.content.substr(msg.content.indexOf(':') + 2, msg.content.length - msg.content.indexOf('.') - 2)), //文字
							href:'', //链接
							close: true, //显示关闭按钮
							speed: 8, //延迟,单位秒,默认8
							bottom:0, //距离底部高度,单位px,默认随机
							color: "#fff", //颜色,默认白色
							old_ie_color:'#000000', //ie低版兼容色,不能与网页背景相同,默认黑色
						}
						$('#danmu').barrager(item);
					}
				} else {
					if(localStorage.getItem(msg["channel"]))
						localStorage.setItem(msg["channel"], localStorage.getItem(msg["channel"]) + '<p>' + msg.datetime + '<br>' + msg.content); // 保存聊天记录
					else
						localStorage.setItem(msg["channel"], '<p>' + msg.datetime + '<br>' + msg.content); // 保存聊天记录
				}
			});

			if(localStorage.userName)
				$('#userName').val(localStorage.userName);

			window.setInterval(function() {
				// 保存uname
				if($('#userName').val() && $('#userName').val() != localStorage.userName) {
					localStorage.userName = $('#userName').val();
				}

				// 自动滚动
				if($('#msgScroll').checkbox("is checked")) {
					var elem = document.getElementById('message');
					elem.scrollTop = elem.scrollHeight;
				}
			}, 800);


			socket.on('update_online_count', function(online_stat){
				online_stat = JSON.parse(online_stat);
				$('#onlineUser').text(online_stat.online_count_now)
			});

			socket.on('update_room_online_count', function(online_stat){
				// console.log(online_stat);
				online_stat = JSON.parse(online_stat);
				$('#roomOnlineUser').text(online_stat[room]);
			});

			// 播放器
			$('#player source').attr("src", "live/" + room + ".m3u8");
			$('#curRoom').html(room);

			var player = videojs('#player');
			player.play();

			// 弹幕
			$('#danmuContent').keypress(function(e) {
				if(e.which == 13) {
					if(! $('#userName').val()) {
						alert('请输入用户名！');
					} else if(! $('#danmuContent').val()) {
						alert('请输入内容！');
					} else {
						$.ajax({
							url: api,
							type: "GET",
							data: {
								send: $('#userName').val() + ": " + $('#danmuContent').val(),
								channel: room
							},
							dataType: "json",
						});

						$('#danmuContent').val("");
					}
				}
			});


		</script>

	</body>
</html>

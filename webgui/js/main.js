$( document ).ready(function() {
	var chatbuffer = 0;
	var input_mode = "chat";
	var help_active = false;
	var info_active = false;
	
	$.get( "inc/commands.php", function( data ) {
		$("body").append(data);
		$("#lastcmds img").click(function() {
			if (help_active != true) {
				$('#commands').fadeIn(200);
				help_active = true;
			} else if (help_active != false) {
				$('#commands').fadeOut(200);
				help_active = false;
			}
		});
		$("#commands a").click(function() {
			if (help_active != false) {
				$('#commands').fadeOut(200);
				help_active = false;
			}
			return false;
		});
	});
	$.get( "inc/info.php", function( data ) {
		$("body").append(data);
		$("#infos a.close").click(function() {
			if (info_active != false) {
				$('#infos').fadeOut(200);
				info_active = false;
			}
			return false;
		});
		$("#addadmin img").click(function() {
			if (info_active != true) {
				$('#infos').fadeIn(200);
				info_active = true;
			} else if (info_active != false) {
				$('#infos').fadeOut(200);
				info_active = false;
			}
		});
	});
	
	$("#chatform img").click(function() {
		var ifactive = $(this).attr("data-active");
		$('#text').val('');
		if (ifactive == "chat") {
			$(this).fadeOut(50, function() {
				$(this).attr("src","img/chat.png").fadeIn(100);
			});
			input_mode = "terminal";
			$(this).attr("data-active", input_mode);
			
			$("#chatform #text").attr("placeholder","Command ...");
		} else if (ifactive == "terminal") {
			$(this).fadeOut(50, function() {
				$(this).attr("src","img/terminal.png").fadeIn(100);
			});
			input_mode = "chat";
			$(this).attr("data-active", input_mode);
			
			$("#chatform #text").attr("placeholder","Text-Chat ...");
		}
		$('#text').focus();
	});
	
	function reloadAdmins() {
		$.get( "inc/admins.php", function( data ) {
			$("#addadmin .inner").html(data);
			$("#addadmin a").click(function() {
				var getadmin = $(this).attr("class");
				var add_admin = "/add_admin " + getadmin;
				
				var getactive = $(this).parent().find('span').text();
				
				if (getactive == "active") {
					add_admin = "/remove_admin " + getadmin;
				}
				
				$.post( "inc/handler.php?a=cmd", { text: add_admin }, function (data) {
					$('#lastcmds .inner').prepend('<div>' + data + '</div>');
				});
				reloadAdmins();
				return false;
			});
		});
	}
	
	function refreshChat() {
		$.get( "inc/data.php?a=chat", function( data ) {
			$("#inner").html(data);
			
			$("#chat table tr").hover(
				function() {
					$(this).css("background","rgba(255,255,255,.15)");
				},
				function() {
					$(this).css("background","");
				}
			);
			
			if (chatbuffer.length != data.length) {
				chatbuffer = data;
				$('#chat').scrollTop( $('#chat #inner').height());
			}
		});
		getPlayers();
		reloadAdmins();
	}
	function getPlayers() {
		$.get( "inc/data.php?a=players", function( data ) {
			$("#players .inner").html(data);
			
			$("#players .inner li").hover(
				function() {
					$(this).css("background","rgba(255,255,255,.15)");
				},
				function() {
					$(this).css("background","");
				}
			);
			var count = $("#players .inner li").length;
			
			$("#count").html(count + ' online');
			
			$("#players .inner li img").click(function() {
				getnick = $(this).attr("data-nickname");
				if(confirm('Spieler ' + getnick + ' wirklich kicken?')) {
					$.post( "inc/handler.php?a=kick", { name: getnick }, function (data) {
					});
				}
			});
		});
	}
	initChat = setInterval(function() {
		refreshChat();
	}, 3000);
	
	refreshChat();
	
	$('#chat').scrollTop( $('#chat #inner').height());
	$('#text').focus();
	
	var myformselector = "#chatform";
	$(myformselector).submit(function(e) {
		e.preventDefault();
		var actionurl = e.currentTarget.action;
		if ($('#text').val().length >1) {
			getmode = $("#chatform img").attr("data-active");
			if (getmode == "chat") {
				$.post( "inc/handler.php?a=chat", $(myformselector).serialize(), function (data) {
					refreshChat();
					$('#text').val('');
				});
			}
			if (getmode == "terminal") {
				$.post( "inc/handler.php?a=cmd", $(myformselector).serialize(), function (data) {
					$('#text').val('').attr("placeholder", "Command executed (no text return)");
					$('#lastcmds .inner').prepend('<div>' + data + '</div>');
				});
			}
		}
	});
});
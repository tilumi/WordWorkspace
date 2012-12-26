$ ->
	addVideoQtip = ->
		videoIndex = 1
		$("a").each ->
			if $(this).text().indexOf("影片") > -1
				$elem = $(this)
				do ($elem, videoIndex) ->
						position = null
						$elem.qtip({
		          content : {prerender: true, text: $("<div id='video_#{videoIndex}'></div>")}
		          show: {
		            solo : true
		            when:'click'
		          },
		          style: {
		              width: 390,
		              height: 230,
		              padding: 0,
		              tip: true,
		              name: 'dark'
		          },
		          hide: {
		            fixed: true,
		            when: {
		              event: 'unfocus'
		            }
		          }
		          api: {
		          	onShow: ->
		          		if position
		          			jwplayer("video_#{videoIndex}").seek(position)
		          			jwplayer("video_#{videoIndex}").pause(true)
		          	beforeHide: ->
		          		console.log videoIndex	
		          		jwplayer("video_#{videoIndex}").pause(true)
		          		position = jwplayer("video_#{videoIndex}").getPosition()
		          		# true
		          }
		        	});
		        jwplayer("video_#{videoIndex}").setup({
		          			file:'rtmp://localhost/vod/mp4:sample.mp4'
		          			width: "385"
		          			height: "225"
		          			primary: "flash"
		        })
		    	$(this).removeAttr("href")
		    	videoIndex++
				window.addVideoQtip = addVideoQtip
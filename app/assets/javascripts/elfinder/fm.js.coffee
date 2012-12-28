# Place all the behaviors and hooks related to the matching controller here.
# All this logic will automatically be available in application.js.
# You can use CoffeeScript in this file: http://jashkenas.github.com/coffee-script/
# $ ->
#   rails_csrf = {}
#   rails_csrf[$("meta[name=csrf-param]").attr("content")] = $("meta[name=csrf-token]").attr("content")
#   $("#elfinder").elfinder
#     lang: "ru"
#     height: "460"
#     url: "/elfinder"
#     transport: new elFinderSupportVer1()
#     customData: rails_csrf

getUrlParam = (paramName) ->
  reParam = new RegExp("(?:[?&]|&)" + paramName + "=([^&]+)", "i")
  match = window.location.search.match(reParam)
  (if (match and match.length > 1) then match[1] else "")
$ ->
  setTimeout( ->
      elfinder = $("#elfinder").elfinder(
        url: "/finder/elfinder"
        lang:	"zh_TW"
        transport: new elFinderSupportVer1()
        # ui:['toolbar', 'places', 'path', 'stat']
        getFileCallback: (file) ->
          if /.mp4$/.test(file)      
            file = file.slice(0,file.lastIndexOf('/')) + file.slice(file.lastIndexOf('/')+1)
            TINY.box.show({
              html:"<div id='video'></div>"
              animate:true
              width: 520
              height: 305
              openjs: ->
                  jwplayer("video").setup({
                    file: file
                    width: "515"
                    height: "300"
                    primary: "flash"
                    autostart: true
                    }
                  )
              })
          else if /(.jpg$)|(.png$)/.test(file)
            console.log file
            files = elfinder.files()
            i = 0
            for key of files
              if /(.jpg$)|(.png$)/.test(files[key].url)
                if file == files[key].url
                  clickedIndex = i
                $("#images").append($("<a class='image' href='/finder/image_proxy?query=#{files[key].url.slice(file.lastIndexOf(':')+2)}'>"))
                i++
            $(".image").each( (index ,element)-> 
                $(this).colorbox({rel:'image',maxWidth: "80%", maxHeight: "80%", transition: 'fade', onClosed: -> $("#images").html("")}) if index != clickedIndex 
            )
            $($(".image").get(clickedIndex)).colorbox({rel:'image',maxWidth: "80%", maxHeight: "80%", transition: 'fade', open: true, onClosed: -> $("#images").html("")})

          ).elfinder('instance')
  ,100)


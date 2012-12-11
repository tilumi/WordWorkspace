$ ->

  rangy.init();

  last_saved_markup_id = parseInt($("#mid").text())
  # last_saved_comment_id = parseInt($("#commentid").text())
  # console.log( $("#mid").text() )
  markup_id = last_saved_markup_id + 1
  removed_markup_ids = []
  added_markup_ids = []
  added_comment_ids = []
  removed_comment_ids = []

  # commandHistroy = []
  markup_class = "markup"

  # class AddMarkupCommand 

  #   constructor: (@markup_id,@class,@serializedRange) ->


  # class RemoveMarkupCommand

  #   constructor: (@serializedMarkup) ->


  markCssApplier = rangy.createCssClassApplier(markup_class, null, ["p","b","span","strong","a","font"]);

  jsPlumb.Defaults.Container = $("body");
  jsPlumb.importDefaults({
        Connector:[ "Flowchart", { stub:10 } ]
        PaintStyle:{ lineWidth:3, strokeStyle:"#ffa500", "dashstyle":"2 4" },
        Endpoint:[ "Dot", { radius:5 } ],
        EndpointStyle:{ fillStyle:"#ffa500"
         }
        ConnectionsDetachable:false
      });
  isRangeStartAndEndInMarkup = (range) ->
    $(range.startContainer).parents().attr("data-range-id") && $(range.endContainer).parents().attr("data-range-id")
  selectedMarkup = null

  $("#save-btn").click( ->
      save()
  )

  # $("#restore-btn").click( ->
  #     restore()
  # )

  reAttachComments = () ->
    saved_comments = JSON.parse($("#saved_comments").text())
    for saved_comment in saved_comments
      comment_id = saved_comment.mid
      $comment = $("<div>", {id : "comment_#{comment_id}"}).addClass("comment")

      $closeButton = $("<img>",{src : "/assets/close.png"}).addClass("close-button").click(
        ->
          removeComment($markups,$comment)
      )
      $comment.append($closeButton)

      $textarea = $('<textarea>')
      $textarea.autosize({append: "\n"})
      $textarea.width(saved_comment.width) if saved_comment.width > 0
      $textarea.height(saved_comment.height) if saved_comment.height > 0
      $textarea.val(saved_comment.content)
      $textarea.on({
        input : addToAddedCommentIDs(comment_id)
        resize : addToAddedCommentIDs(comment_id)        
      })
      $comment.append($textarea)
      $comment.on({
        mousedown : ->
          this.mouseDownPosition = $(this).position()
        mouseup: ->
          if this.mouseDownPosition
            if $(this).position().top != this.mouseDownPosition.top or $(this).position().left != this.mouseDownPosition.left
              addToAddedCommentIDs(comment_id)
      })

      $markups = $("[data-range-id='#{comment_id}']")
      $comment.css({top: "#{saved_comment.y}px", left:"#{saved_comment.x}px"}).addClass("absolute")      
      jsPlumb.draggable($comment)
      $("#comments").append($comment)

      $connect = jsPlumb.connect({
        source: $markups[0]
        target: $comment
        anchors: ["TopCenter","TopCenter"]
      })
      if selectedMarkup and selectedMarkup.$markups.is $markups
        selectedMarkup.$comment = $comment
        selectedMarkup.$connect = $connect

      $textarea.focus( ->
          clickMarkup($(this).closest(".comment"),null,$(this))
      )
      $textarea.focus()

  addToAddedCommentIDs = (comment_id) ->
    if added_comment_ids.indexOf(comment_id) == -1
      added_comment_ids.push(comment_id)
    if removed_comment_ids.indexOf(comment_id) > -1
      removed_comment_ids.splice(removed_comment_ids.indexOf(comment_id),1)

  save = () ->

    markupsToAdd = []
    
    for i in added_markup_ids
      $markupToAdd = $("[data-range-id='#{i}']")
      if $markupToAdd.size() > 0
        markupToAdd = {}
        markupToAdd.mid = i
        markupToAdd.className = $markupToAdd.get(0).className
        markupToAdd.content =  $markupToAdd.text()
        markupsToAdd.push(markupToAdd)

    commentsToAdd = []

    for i in added_comment_ids
      $commentToAdd = $("#comment_#{i}")
      if $commentToAdd.size() > 0
        commentToAdd = {}
        commentToAdd.mid = i
        commentToAdd.className = $commentToAdd.get(0).className
        commentToAdd.content = $commentToAdd.find("textarea").val()
        commentToAdd.x = $commentToAdd.position().left
        commentToAdd.y = $commentToAdd.position().top
        commentToAdd.width = $commentToAdd.find("textarea").width()
        commentToAdd.height = $commentToAdd.find("textarea").height()
        commentsToAdd.push(commentToAdd)
        # console.log($commentToAdd.position())

    $.ajax({
      url : 'save'
      type : 'POST'
      data: {
        html :  $("#doc").html()
        markupsToAdd : markupsToAdd
        markupsToDelete : ( item for item in removed_markup_ids when parseInt(item) <= last_saved_markup_id )
        commentsToAdd : commentsToAdd
        commentsToDelete : ( item for item in removed_comment_ids when parseInt(item) <= last_saved_markup_id )

      }
      error: (xhr) ->
      success: (response) ->
        added_markup_ids.length = 0
        removed_markup_ids.length = 0
        added_comment_ids.length = 0
        removed_comment_ids.length = 0
        last_saved_markup_id = markup_id - 1
    })
    
  $.contextMenu(
    {
        selector: 'span.markup',
        callback: (key, options) ->
          switch key
            when "delete" then removeMarkup(this)
            when "edit" then addComment(this)
        ,
        items: {
          "edit": {name: "Add Comment", icon: "edit"},
          "cut": {name: "Cut", icon: "cut"},
          "copy": {name: "Copy", icon: "copy"},
          "paste": {name: "Paste", icon: "paste"},
          "delete": {name: "Delete", icon: "delete"},
          "sep1": "---------",
          "quit": {name: "Quit", icon: "quit"}
        }
    }
  )

  addComment = (elem) ->
    comment_id = $(elem).attr("data-range-id")
    $comment = $("<div>", {id : "comment_#{comment_id}"}).addClass("comment")

    $closeButton = $("<img>",{src : "/assets/close.png"}).addClass("close-button").click(
      ->
        removeComment($markups,$comment)
    )
    $comment.append($closeButton)

    $textarea = $('<textarea>')
    $textarea.autosize({append: "\n"})
    $textarea.on({
      input : addToAddedCommentIDs(comment_id)
      resize : addToAddedCommentIDs(comment_id)
    })
    $comment.append($textarea)

    $markups = $("[data-range-id='#{comment_id}']")
    $comment.css({top: "#{$($markups[0]).position().top - $($markups[0]).closest('#doc').position().top}px", left:"0px"}).addClass("absolute")
    jsPlumb.draggable($comment)
    $("#comments").append($comment)

    $connect = jsPlumb.connect({
      source: $markups[0]
      target: $comment
      anchors: ["TopCenter","TopCenter"]
    })
    if selectedMarkup and selectedMarkup.$markups.is $markups
      selectedMarkup.$comment = $comment
      selectedMarkup.$connect = $connect

    $textarea.focus( ->
        clickMarkup($(this).closest(".comment"),null,$(this))
    )
    $textarea.focus()

    added_comment_ids.push(comment_id)
    if removed_comment_ids.indexOf(comment_id) > -1
      removed_comment_ids.splice(removed_comment_ids.indexOf(comment_id),1)
    console.log(removed_comment_ids)
    console.log(added_comment_ids)
    History.do(new AddCommentMemento($markups,$comment))

  $("#doc").bind 'mouseup', (e) ->
    selection = rangy.getSelection()
    range = selection.getRangeAt(0)
    unless range.collapsed
      selection.removeAllRanges()
      addMarkup(range)

  $("#nav").find("a").addClass("unselectable").on( "onselectstart" , ->
        false
  )

  removeComment = ($markups,$comment)->
    History.do(new DeleteCommentMemento($markups,$comment))
    comment_id = $comment.attr("id").split("_")[1]
    removed_comment_ids.push(comment_id)
    if added_comment_ids.indexOf(comment_id) > -1
      added_comment_ids.splice(added_comment_ids.indexOf(comment_id), 1)
    console.log(removed_comment_ids)
    console.log(added_comment_ids)
    jsPlumb.removeAllEndpoints($comment)
    $comment.detach()

  applyMarkupHover = ($comment,$markup)->
    comment_id = getCommentID($comment,$markup)
    $("[data-range-id='#{comment_id}']").addClass("markup-hover")
    $("#comment_#{comment_id}").addClass("markup-hover")
    if $connect = jsPlumb.select({target : "comment_#{comment_id}"}).get(0)
      $connect.setVisible(true)
      $connect.endpoints[0].setVisible(true)
      $connect.endpoints[1].setVisible(true)

  unapplyMarkupHover = ($comment,$markup)->

    comment_id = getCommentID($comment,$markup)
    if !selectedMarkup or !selectedMarkup.$comment.is($("#comment_#{comment_id}"))
      $("[data-range-id='#{comment_id}']").removeClass("markup-hover")
      $("#comment_#{comment_id}").removeClass("markup-hover")
      if $connect = jsPlumb.select({target : "comment_#{comment_id}"}).get(0)
        $connect.setVisible(false)
        $connect.endpoints[0].setVisible(false)
        $connect.endpoints[1].setVisible(false)

  getCommentID = ($comment,$markup) ->
    if $comment
      comment_id = $comment.attr("id").split("_")[1]
    if $markup
      comment_id = $markup.attr("data-range-id")
    comment_id

  clickMarkup = ($comment,$markup,$target) ->
    if selectedMarkup
      if $markup && selectedMarkup.$markups.index($markup) >= 0
        unselectMarkup()
        selectedMarkup = null
        return
      if $comment && selectedMarkup.$comment.is($comment) && !$target.is($comment.find("textarea"))
        unselectMarkup()
        selectedMarkup = null
        return
    unselectMarkup()
    comment_id = getCommentID($comment,$markup)
    $comment = $("#comment_#{comment_id}") unless $comment
    $comment.addClass("markup-selected") if $comment.size() > 0
    $markups = $("[data-range-id='#{comment_id}']").addClass("markup-selected")
    if $comment.size() > 0
      $connect = jsPlumb.select({target : $comment}).get(0)
      if $connect
        $connect.setVisible(true)
        $connect.endpoints[0].setVisible(true)
        $connect.endpoints[1].setVisible(true)
    selectedMarkup = {
      $comment : $comment
      $markups : $markups
      $connect : $connect
    }

  unselectMarkup = ->
    if selectedMarkup
      if selectedMarkup.$comment
        selectedMarkup.$comment.removeClass("markup-selected").removeClass("markup-hover")
      if selectedMarkup.$markups
        selectedMarkup.$markups.removeClass("markup-selected").removeClass("markup-hover")
      if selectedMarkup.$connect
        selectedMarkup.$connect.setVisible(false)
        selectedMarkup.$connect.endpoints[0].setVisible(false)
        selectedMarkup.$connect.endpoints[1].setVisible(false)


  $("body").on(
    {
      mouseenter: ->
        applyMarkupHover(null,$(this))

      mouseleave: ->
        unapplyMarkupHover(null,$(this))

      click: (e) ->
        clickMarkup(null,$(this),null)
    }
    ".markup"
  )

  $("body").on(
    {
      mouseenter: ->
        applyMarkupHover($(this))

      mouseleave: ->
        unapplyMarkupHover($(this))

      click: (e) ->
        clickMarkup($(this),null,$(e.target))
    }
    ".comment"
  )


  addMarkup = (range) ->
    # commandHistroy.push(new AddMarkupCommand(rangy.serializeRange(range,true,$("#doc").get(0))))
    addMarkupMemento = new AddMarkupMemento(markup_id)
    History.do(addMarkupMemento)
    markCssApplier.applyToRange(range,markup_id,addMarkupMemento)
    added_markup_ids.push("#{markup_id}")
    if removed_markup_ids.indexOf("#{markup_id}") > -1
      removed_markup_ids.splice(removed_markup_ids.indexOf("#{markup_id}"),1)
    console.log(removed_markup_ids)
    console.log(added_markup_ids)
    markup_id++

  removeMarkup = (markup) ->
    # commandHistroy.push(new RemoveMarkupCommand(rangy.serializePosition($(markup).get(0),0,$("#doc").get(0))))
    comment_id = $(markup).attr("data-range-id")
    removed_markup_ids.push("#{comment_id}")
    if added_markup_ids.indexOf("#{comment_id}") > -1
      added_markup_ids.splice(added_markup_ids.indexOf("#{comment_id}"),1)
    $markupsToDelete = $("[data-range-id='#{comment_id}']")
    History.beginCompoundDo()
    $comment = $("#comment_#{comment_id}")
    if $comment.size() > 0
      History.do(new DeleteCommentMemento($markupsToDelete,$comment))
    removeMarkupMemento = new RemoveMarkupMemento(comment_id)
    History.do(removeMarkupMemento)
    $connect = jsPlumb.removeAllEndpoints($comment) if $comment
    $markupsToDelete.each((index) ->
        markCssApplier.removeMarkup(this.parentNode,this.childNodes[0],this,removeMarkupMemento)
    )
    $comment.detach()
    console.log(removed_markup_ids)
    console.log(added_markup_ids)
    History.endCompoundDo()

  $("#undo-btn").click ->
      # commandHistroy.push("undo")
      History.undo()
      # console.log(removed_markup_ids)
      # console.log(added_markup_ids)


  $("#redo-btn").click ->
      # commandHistroy.push("redo")
      History.redo()
      # console.log(removed_markup_ids)
      # console.log(added_markup_ids)
  
  class AddMarkupMemento

    constructor: (@markup_id)->
      @_mementos = []

    push: (m) ->
      @_mementos.push(m)

    restore: ->
      if added_markup_ids.indexOf(""+@markup_id) > -1
        added_markup_ids.splice(added_markup_ids.indexOf(""+@markup_id),1)
      removed_markup_ids.push(@markup_id.toString())
      inverse = new RemoveMarkupMemento(@markup_id)
      inverse.push(m.restore()) for m in @_mementos.reverse()
      inverse

  class RemoveMarkupMemento

    constructor: (@markup_id)->
      @_mementos = []

    push: (m) ->
      @_mementos.push(m)

    restore: ->
      if removed_markup_ids.indexOf(@markup_id.toString()) > -1
        removed_markup_ids.splice(removed_markup_ids.indexOf(@markup_id.toString()),1)
      added_markup_ids.push(@markup_id.toString())
      inverse = new AddMarkupMemento(@markup_id)
      inverse.push(m.restore()) for m in @_mementos.reverse()
      inverse

  class AddCommentMemento

    constructor: (@markups,@comment) ->

    restore: ->
      if @comment.size() > 0
        jsPlumb.removeAllEndpoints(@comment) 
        @comment.detach()
        comment_id = $(@comment).attr("id").split("_")[1]
        removed_comment_ids.push(comment_id)
        if added_comment_ids.indexOf(comment_id) > -1
          added_comment_ids.splice(added_comment_ids.indexOf(comment_id), 1)
      console.log(removed_comment_ids)
      console.log(added_comment_ids)
      new DeleteCommentMemento(@markups,@comment)


  class DeleteCommentMemento

    constructor: (@markups,@comment) ->

    restore: ->
      if(@comment.size() > 0)
        comment_id = $(@comment).attr("id").split("_")[1]
        added_comment_ids.push(comment_id)
        if removed_comment_ids.indexOf(comment_id) > -1
          removed_comment_ids.splice(removed_comment_ids.indexOf(comment_id),1)
        $("#comments").append(@comment)
        $connect = jsPlumb.connect({
          source: @markups[0]
          target: @comment
          anchors: ["TopCenter","TopCenter"]
        })
        @comment.find("textarea").focus()
        if selectedMarkup.$markups.is(@markups)
          selectedMarkup.$connect = $connect
      console.log(removed_comment_ids)
      console.log(added_comment_ids)
      new AddCommentMemento(@markups,@comment)

  reAttachComments()

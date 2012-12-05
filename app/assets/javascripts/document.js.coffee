$ ->

  rangy.init();

  markup_id = 1
  markCssApplier = rangy.createCssClassApplier("markup", null, ["p","b","span","strong","a","font"]);

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
    $(range.startContainer).parents().hasClass("markup") && $(range.endContainer).parents().hasClass("markup")
  selectedMarkup = null

  $.contextMenu(
    {
        selector: 'span.markup',
        callback: (key, options) ->
          switch key
            when "delete" then deleteMarkup(this)
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
        History.do(new DeleteCommentMemento($markups,$comment))
        jsPlumb.detach($connect)
        $comment.detach()
    )
    $comment.append($closeButton)

    $textarea = $('<textarea>')
    $textarea.autosize({append: "\n"})
    $comment.append($textarea)

    $markups = $("[data-range-id='#{comment_id}']")
    $comment.css({top: "#{$($markups[0]).position().top - $($markups[0]).closest('#doc').position().top}px", left:"0px"}).addClass("absolute")
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
    History.do(new AddCommentMemento($markups,$comment,$connect))

  $("#doc").bind 'mouseup', (e) ->
    selection = rangy.getSelection()
    range = selection.getRangeAt(0)
    unless range.collapsed
      selection.removeAllRanges()
      History.beginCompoundDo()
      markCssApplier.applyToRange(range,markup_id)
      History.endCompoundDo()
      markup_id++

  $("#nav").find("a").addClass("unselectable").on( "onselectstart" , ->
        false
  )

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

  deleteMarkup = (markup) ->
    comment_id = $(markup).attr("data-range-id")
    $markupsToDelete = $("[data-range-id='#{comment_id}']")
    History.beginCompoundDo()
    $comment = $("#comment_#{comment_id}")
    History.do(new DeleteCommentMemento($markupsToDelete,$comment))
    $markupsToDelete.each((index) ->
        console.log (this.childNodes[0])
        this.parentNode.insertBefore(this.childNodes[0],this)
        $(this).remove()
    )
    $connect = jsPlumb.select({target : $comment}).get(0) if $comment
    jsPlumb.deleteEndpoint($connect.endpoints[0]) if $connect
    $comment.detach()
    History.endCompoundDo()

  getCommonAncestor = (a, b) ->

    $parentsa = $(a).parents()
    $parentsb = $(b).parents()
    found = null
    $parentsa.each(->
      thisa = this

      $parentsb.each(->
          if (thisa == this)
            found = this
            return false
      )
      return false if found
    )
    found

  $("#undo-btn").click ->
      History.undo()


  $("#redo-btn").click ->
      History.redo()

  class AddCommentMemento

    constructor: (@markups,@comment,@connect) ->

    restore: ->
      jsPlumb.detach(@connect)
      @comment.detach()
      new DeleteCommentMemento(@markups,@comment)


  class DeleteCommentMemento

    constructor: (@markups,@comment) ->

    restore: ->
      $("#comments").append(@comment)
      $connect = jsPlumb.connect({
        source: @markups[0]
        target: @comment
        anchors: ["TopCenter","TopCenter"]
      })
      @comment.find("textarea").focus()
      if selectedMarkup.$markups.is(@markups)
        selectedMarkup.$connect = $connect
      new AddCommentMemento(@markups,@comment,$connect)

  class MarkupMemento

    constructor: (@node , @xml) ->

    restore: ->
      state = new MarkupMemento(@node, (new XMLSerializer()).serializeToString(rangy.deserializePosition(@node,$("#doc")[0]).node) )
      $(rangy.deserializePosition(@node,$("#doc")[0]).node).replaceWith(@xml)
      state

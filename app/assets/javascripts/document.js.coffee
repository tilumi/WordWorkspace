$ ->

  rangy.init();

  last_saved_markup_id = parseInt($("#last_saved_markup_id").text())
  last_saved_outline_id = parseInt($("#last_saved_outline_id").text())
  undoThreshold = 0
  markup_id = last_saved_markup_id + 1
  current_outline_id = last_saved_outline_id + 1
  removed_markup_ids = []
  added_markup_ids = []
  added_comment_ids = []
  removed_comment_ids = []
  added_outline_ids = []
  removed_outline_ids = []
  commandHistroy = []
  MARKUP_MODE = 1
  OUTLINE_MODE = 2
  edit_mode = 1
  selectedMarkup = null
  markup_class = "markup"
  markCssApplier = rangy.createCssClassApplier(markup_class, null, ["p","b","span","strong","a","font"]);

  jsPlumb.Defaults.Container = $("body");
  commentConOptions = {
        # connector:[ "Flowchart", { stub:10 } ]
        connector : [ "Bezier", { curviness: 50 } ]
        paintStyle:{ lineWidth:3, strokeStyle:"#ffa500", "dashstyle":"2 4" }
        endpoint:[ "Dot", { radius:5 } ],
        endpointStyle:{ fillStyle:"#ffa500" }
        connectionsDetachable:false
      }

  outlinesConOptions = {
        connector : [ "Bezier", { curviness:100 } ]
        paintStyle:{ lineWidth:3, strokeStyle:"#ffa500", "dashstyle":"2 4" }
        cssClass : 'outline'
        hoverClass : 'outline-hover'
      }

  outlineEndpointOptions = {
            isSource:true
            endpoint:[ "Dot", { radius:5 } ],
            paintStyle:{ fillStyle:"#ffa500" }
          }

  dropOptions = {
        tolerance:'touch',
        hoverClass:'dropHover',
        activeClass:'dragActive',
        zIndex : 5,
      };

  jsPlumb.bind('connectionDragStop', (connection) ->
      jsPlumb.selectEndpoints({scope:connection.endpoints[1].scope}).each((endpoint) ->
        if endpoint.connections.length == 0
            endpoint.setVisible(false)
      )
    )

  originalId = null

  class ChangeEndpointMemento

    constructor: (@originalId, @currentId, @scope, @outline)->

    restore: ->
      console.log @originalId
      console.log @currentId
      console.log @scope
      console.log @outline
      scopeAnchorHash = new Object()
      scopeAnchorHash['outline-start'] = [ 0,0.4,-1,0]
      scopeAnchorHash['outline-end'] = [ 0,0.6,-1,0]
      jsPlumb.select({source: @outline.attr('id') ,scope: @scope}).each((connection) ->
          if connection.endpoints[1].connections.length <= 1
            connection.endpoints[1].setVisible(false)
          jsPlumb.deleteEndpoint(connection.endpoints[0])

        )
      targetEndpoint = jsPlumb.selectEndpoints({scope:@scope,target:@originalId}).get(0)
      targetEndpoint.setVisible(true)
      sourceEndpoint = jsPlumb.addEndpoint(@outline,
        {
          anchor: scopeAnchorHash[@scope]
        },outlineEndpointOptions)
      sourceEndpoint.setDragAllowedWhenFull(false)
      jsPlumb.connect(
        {source: sourceEndpoint
        target : targetEndpoint
        scope : @scope},
        outlinesConOptions
        )
      addToAddedOutlineIDs(@outline.attr("id").split("_")[1],@outline.attr("data-outline-id"))
      new ChangeEndpointMemento(@currentId, @originalId, @scope, @outline)
  setTimeout(() ->
    $("p").each( -> 

      bindEventsOnEndpoints = (endpoint, scope) ->
        endpoint.bind('mouseenter', (endpoint,event) ->
          jsPlumb.selectEndpoints({scope:scope}).each((endpoint) ->
              endpoint.setVisible(true)
          )
        )

        endpoint.bind('mouseexit', (endpointTarget,event) ->
          jsPlumb.selectEndpoints({scope:scope}).each((endpoint) ->
              if endpoint.connections.length == 0
                endpoint.setVisible(false)
          )
        )

      if($(this).text().length > 0 && /\S/gm.test($(this).text()))
        
        endpointStart = jsPlumb.addEndpoint(this,
          {
            isTarget:true
            anchor : [1,0,1,0]
            scope : 'outline-start'
            endpoint:[ "Dot", { radius:10, cssClass : 'outline', hoverClass : 'outline-target-hover' } ],
            paintStyle:{ fillStyle:"#ffa500" }
            dropOptions : dropOptions
            reattach : true
            maxConnections : -1
            beforeDrop: (params) ->
              # console.log originalId
              # console.log params.targetId
              History.do(new ChangeEndpointMemento(originalId, params.targetId,'outline-start',$("##{params.sourceId}")))
              addToAddedOutlineIDs(params.sourceId.split("_")[1],$("##{params.sourceId}").attr("data-outline-id"))
              true
            beforeDetach: (connection) ->
              originalId = connection.targetId
          })
        endpointStart.setVisible(false)

        endpointEnd = jsPlumb.addEndpoint(this,
          {
            isTarget:true
            anchor : [1,1,1,0]
            scope : 'outline-end'
            endpoint:[ "Dot", { radius:10, cssClass : 'outline', hoverClass : 'outline-target-hover' } ],
            paintStyle:{ fillStyle:"#ffa500" }
            dropOptions : dropOptions
            maxConnections : -1
            reattach : true
            beforeDrop: (params) ->
              # console.log params.targetId
              History.do(new ChangeEndpointMemento(originalId, params.targetId,'outline-end',$("##{params.sourceId}")))
              addToAddedOutlineIDs(params.sourceId.split("_")[1],$("##{params.sourceId}").attr("data-outline-id"))
              true
            beforeDetach: (connection) ->
              originalId = connection.targetId
          })
        endpointEnd.setVisible(false)
        bindEventsOnEndpoints(endpointStart,'outline-start')
        bindEventsOnEndpoints(endpointEnd,'outline-end')
    )
  ,100)
  isRangeStartAndEndInMarkup = (range) ->
    $(range.startContainer).parents().attr("data-range-id") && $(range.endContainer).parents().attr("data-range-id")

  
  class SurroundContentsMemento

    constructor: (@span) ->

    restore: ->

      contents = $(@span).contents()
      $(@span).contents().insertBefore(@span)
      $(@span).remove()
      range = rangy.createRange()
      range.setStartBefore(contents.get(0))
      range.setEndAfter(contents.get(contents.size() - 1))
      range.normalizeBoundaries()


  reMarkupAfterLoaded = () ->
    text_ranges = JSON.parse($("#text_ranges").text())
    for text_range in text_ranges
      range = document.createRange()
      # console.log(text_range.start_position)
      deserializedPosition = rangy.deserializePosition(text_range.start_position,$("#doc").get(0))
      deserializedNode = deserializedPosition.node
      if deserializedNode.nodeType != 3
        deserializedNode = deserializedNode.childNodes[0]
      range.setStart(deserializedNode, deserializedPosition.offset )
      range.setEnd(deserializedNode, deserializedPosition.offset + text_range.length)
      newMarkupSpan = $( "<span class='#{text_range.className}' data-range-id='#{text_range.mid}' data-id='#{text_range.id}'>").get(0)
      range.surroundContents(newMarkupSpan)
      if newMarkupSpan.previousSibling.nodeType == 3 && newMarkupSpan.previousSibling.length == 0
        $(newMarkupSpan.previousSibling).remove()
      if newMarkupSpan.nextSibling.nodeType == 3 && newMarkupSpan.nextSibling.length == 0
        $(newMarkupSpan.nextSibling).remove()  
      History.do(new SurroundContentsMemento(newMarkupSpan))
    

  reAttachCommentsAfterLoaded = () ->
    saved_comments = JSON.parse($("#saved_comments").text())
    for saved_comment in saved_comments
      do (saved_comment) ->
        comment_id = saved_comment.mid
        $comment = $("<div>", {id : "comment_#{comment_id}"}).addClass("comment").attr("data-id",saved_comment.id)

        $closeButton = $("<img>",{src : "/assets/close.png"}).addClass("close-button").click(
          ->
            removeComment($markups,$comment)
        )
        $comment.append($closeButton)

        $textarea = $('<textarea>')
        $textarea.autosize({append: "\n"})
        $textarea.width(saved_comment.width) if saved_comment.width > 0
        $textarea.height(saved_comment.height) if saved_comment.height > 0
        # $textarea.val(saved_comment.content)
        $textarea.on({
          input : addToAddedCommentIDs(comment_id, saved_comment.id)
          resize : addToAddedCommentIDs(comment_id, saved_comment.id)        
        })
        $comment.append($textarea)
        $comment.on({
          mousedown : ->
            this.mouseDownPosition = $(this).position()
          mouseup: ->
            if this.mouseDownPosition
              if $(this).position().top != this.mouseDownPosition.top or $(this).position().left != this.mouseDownPosition.left
                addToAddedCommentIDs(comment_id, saved_comment.id)
        })

        $markups = $("[data-range-id='#{comment_id}']")
        $comment.css({top: "#{saved_comment.y}px", left:"#{saved_comment.x}px", position: "absolute"})    
        $("#comments").append($comment)

        $textarea.focus( ->
            clickMarkup($(this).closest(".comment"),null,$(this))
        )
        History.do(new AddCommentMemento($markups,$comment))
        added_comment_ids.length = 0

  connectParagraphToOutline = ($startParagraph, $endParagraph, $outline) ->
    startTargetEndpoint = jsPlumb.getEndpoints($startParagraph).filter((endpoint) -> endpoint.scope == "outline-start")[0]
    startTargetEndpoint.setVisible(true)
    startSourceEndpoint = jsPlumb.addEndpoint($outline,
      {
        anchor: [ 0,0.4,-1,0]
      },outlineEndpointOptions)
    startSourceEndpoint.setDragAllowedWhenFull(false)
    jsPlumb.connect(
      {source: startSourceEndpoint
      target : startTargetEndpoint
      scope : 'outline-start'},
      outlinesConOptions
      )
    endTargetEndpoint = jsPlumb.getEndpoints($endParagraph).filter((endpoint) -> endpoint.scope == "outline-end")[0]
    endTargetEndpoint.setVisible(true)
    endSourceEndpoint = jsPlumb.addEndpoint($outline,
      {
        anchor: [ 0,0.6,-1,0]
      },outlineEndpointOptions)
    endSourceEndpoint.setDragAllowedWhenFull(false)
    jsPlumb.connect(
      {source: endSourceEndpoint
      target : endTargetEndpoint
      scope : 'outline-end'},
      outlinesConOptions
    )
    jsPlumb.draggable($outline)  

  reAttachOutlinesAfterLoaded = () ->
    saved_outlines = JSON.parse($("#outlines").text())
    for saved_outline in saved_outlines
      do(saved_outline) ->
        $startParagraph = $(rangy.deserializePosition(saved_outline.start_paragraph, $("#doc").get(0)).node)
        $endParagraph = $(rangy.deserializePosition(saved_outline.end_paragraph, $("#doc").get(0)).node)
        outline_id = saved_outline.oid
        $outline = $("<div>", {id : "outline_#{outline_id}"}).addClass(saved_outline.className)
        if saved_outline.id
          $outline.attr("data-outline-id",saved_outline.id)
        $outline.css({top: "#{saved_outline.y}px", left: "#{saved_outline.x}px", position: "absolute"})
        $closeButton = $("<img>",{src : "/assets/close.png"}).addClass("close-button").click(
          ->
            removeOutline($startParagraph, $endParagraph, $outline)
        )
        $outline.append($closeButton)
        
        $textarea = $('<textarea>')
        $textarea.autosize({append: "\n"})
        $textarea.width(saved_outline.width)
        $textarea.height(saved_outline.height)
        $textarea.val(saved_outline.content)
        $textarea.on({
          input : addToAddedOutlineIDs(outline_id, $outline.attr("data-outline-id"))
          resize : addToAddedOutlineIDs(outline_id, $outline.attr("data-outline-id"))
          focus : ->        
            this.hasFocus = true
            $outline.addClass('outline-hover')
            jsPlumb.select({source:$outline.attr('id')}).each(
              (connection) ->
                connection.setHover(true)
            )
          focusout : ->
            this.hasFocus = false
            $outline.removeClass('outline-hover')
            jsPlumb.select({source:$outline.attr('id')}).each(
              (connection) ->
                connection.setHover(false)
            )
        })
        $outline.on({
          mousedown : ->
            this.mouseDownPosition = $(this).position()
          mouseup: ->
            if this.mouseDownPosition
              if $(this).position().top != this.mouseDownPosition.top or $(this).position().left != this.mouseDownPosition.left
                addToAddedOutlineIDs(outline_id, $outline.attr("data-outline-id"))
        })
        $outline.append($textarea)

        $("#comments").append($outline)
        setTimeout(() -> 
            connectParagraphToOutline($startParagraph, $endParagraph, $outline)
          ,100)
        History.do(new AddOutlineMemento($startParagraph, $endParagraph, $outline))
        undoThreshold = History._undoStack.length
        added_outline_ids.length = 0


  addVideoQtip = ->
    $("a").each ->
      if $(this).text().indexOf("影片") > -1
        $(this).qtip({
          content : $('<iframe width="380" height="220" src="http://www.youtube.com/embed/yqBKrz9auGo" frameborder="0" allowfullscreen></iframe>')
          show: {
            solo : true
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
        });
        $(this).attr("href","#")

  addToAddedCommentIDs = (mid,id) ->
    if added_comment_ids.indexOf(mid) == -1
      added_comment_ids.push(mid)
    if id
      if removed_comment_ids.indexOf(id) > -1
        removed_comment_ids.splice(removed_comment_ids.indexOf(id),1)

  addToRemovedCommentIDs = (mid,id) ->
    if id
      if removed_comment_ids.indexOf(id) == -1
        removed_comment_ids.push(id)
    if added_comment_ids.indexOf(mid) > -1
      added_comment_ids.splice(added_comment_ids.indexOf(mid),1)

  addToAddedOutlineIDs = (oid, id) ->
    createAddToOutlineIDsFunc(added_outline_ids,removed_outline_ids).call(this, "#{oid}", "#{id}")

  addToRemovedOutlineIDs = (oid, id) ->
    createAddToOutlineIDsFunc(removed_outline_ids,added_outline_ids).call(this, "#{id}", "#{oid}")

  createAddToOutlineIDsFunc = (outline_ids_to_add, outline_ids_to_remove)->
    (oid, id) ->
      if oid
        if outline_ids_to_add.indexOf(oid) == -1
          outline_ids_to_add.push(oid)
      if id
        if outline_ids_to_remove.indexOf(id) > -1
          outline_ids_to_remove.splice(outline_ids_to_remove.indexOf(id),1)

  save = () ->

    getStartPosition = (span) ->
      if span.previousSibling and span.previousSibling.nodeType == 3
        rangy.serializePosition(span.previousSibling,span.previousSibling.length,$("#doc").get(0))
      else
        rangy.serializePosition(span,0,$("#doc").get(0))

    markupsToAdd = []
    
    for i in added_markup_ids
      $markupToAdd = $("[data-range-id='#{i}']")
      if $markupToAdd.size() > 0
        markupToAdd = {}
        markupToAdd.mid = i
        markupToAdd.className = $markupToAdd.get(0).className.split(" ")[0]
        markupToAdd.content =  $markupToAdd.text()
        markupToAdd.textRanges = []
        

        markupsToAdd.push(markupToAdd)

    commentsToAdd = []

    # console.log added_comment_ids

    for i in added_comment_ids
      $commentToAdd = $("#comment_#{i}")
      if $commentToAdd.size() > 0
        commentToAdd = {}
        commentToAdd.mid = i
        commentToAdd.id = $commentToAdd.attr("data-id")
        # commentToAdd.className = $commentToAdd.get(0).className
        commentToAdd.content = $commentToAdd.find("textarea").val()
        commentToAdd.x = $commentToAdd.position().left
        commentToAdd.y = $commentToAdd.position().top
        commentToAdd.width = $commentToAdd.find("textarea").width()
        commentToAdd.height = $commentToAdd.find("textarea").height()
        commentsToAdd.push(commentToAdd)

    textRanges = []

    $(".markup").each(
      ->
        textRange = {}
        textRange.id = $(this).attr("data-id")
        textRange.mid = $(this).attr("data-range-id")
        textRange.start_position = getStartPosition(this)
        textRange.length = $(this).text().length
        textRanges.push(textRange)
    )

    outlinesToAdd = []

    for i in added_outline_ids
      outline = {}
      $outline = $("#outline_#{i}")
      outline.id = $outline.attr("data-outline-id")
      outline.oid = parseInt(i)
      # console.log jsPlumb.select({source:"outline_#{i}", scope: 'outline-start'}).get(0).target
      outline.start_paragraph = rangy.serializePosition(jsPlumb.select({source:"outline_#{i}",scope:'outline-start' }).get(0).target.get(0),0,$("#doc").get(0))
      outline.end_paragraph = rangy.serializePosition(jsPlumb.select({source:"outline_#{i}", scope: 'outline-end' }).get(0).target.get(0),0,$("#doc").get(0))
      outline.x = $outline.position().left
      outline.y = $outline.position().top
      outline.width = $outline.find("textarea").width()
      outline.height = $outline.find("textarea").height()
      outline.className = $outline.get(0).className.split(" ")[0]
      outline.content = $outline.find("textarea").val()
      outlinesToAdd.push(outline)    

    $.ajax({
      url : 'save'
      type : 'POST'
      data: {
        markupsToAdd : markupsToAdd
        markupsToDelete : removed_markup_ids.filter( (mid) -> parseInt(mid) <= last_saved_markup_id )
        commentsToAdd : commentsToAdd
        commentsToDelete : removed_comment_ids
        outlinesToAdd : outlinesToAdd
        outlinesToDelete : removed_outline_ids
        textRanges : textRanges

      }
      error: (xhr) ->
      success: (response) ->
        added_markup_ids.length = 0
        removed_markup_ids.length = 0
        added_comment_ids.length = 0
        removed_comment_ids.length = 0
        added_outline_ids.length = 0
        removed_outline_ids.length = 0
        last_saved_markup_id = markup_id - 1
        last_saved_outline_id = current_outline_id - 1
        if response['text_range_mid_pk_hash']
          mid_pk_hash = response['text_range_mid_pk_hash']
          for mid of mid_pk_hash
            $("[data-range-id=#{mid}]").each( 
              ->
                $(this).attr("data-id",mid_pk_hash[mid])
            )
        if response['comment_mid_pk_hash']
          mid_pk_hash = response['comment_mid_pk_hash']
          for mid of mid_pk_hash
            $("#comment_#{mid}").each( 
              ->
                $(this).attr("data-id",mid_pk_hash[mid])
            )
        if response['outline_oid_pk_hash']
          outline_oid_pk_hash = response['outline_oid_pk_hash']
          for oid of outline_oid_pk_hash
            $("#outline_#{oid}").each(
              ->
                $(this).attr("data-outline-id",outline_oid_pk_hash[oid])
            )

        $("#info").text("Document saved!").fadeIn(100).fadeOut(1000)

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
          # "cut": {name: "Cut", icon: "cut"},
          # "copy": {name: "Copy", icon: "copy"},
          # "paste": {name: "Paste", icon: "paste"},
          "delete": {name: "Delete", icon: "delete"},
          # "sep1": "---------",
          # "quit": {name: "Quit", icon: "quit"}
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
    $textarea.width(180)
    $textarea.height(40)
    $textarea.on({
      input : addToAddedCommentIDs(comment_id, $comment.attr("data-id"))
      resize : addToAddedCommentIDs(comment_id, $comment.attr("data-id"))
    })
    $comment.append($textarea)
    $comment.on({
          mousedown : ->
            this.mouseDownPosition = $(this).position()
          mouseup: ->
            if this.mouseDownPosition
              if $(this).position().top != this.mouseDownPosition.top or $(this).position().left != this.mouseDownPosition.left
                addToAddedCommentIDs(comment_id, $comment.attr("data-id"))
        })

    $markups = $("[data-range-id='#{comment_id}']")
    $comment.css({top: "#{$($markups[0]).position().top - $($markups[0]).closest('#doc').position().top}px", left:"0px", position : 'absolute'})
    $("#comments").append($comment)
    if selectedMarkup and selectedMarkup.$markups.is $markups
      selectedMarkup.$comment = $comment
      # selectedMarkup.$connect = $connect
    $textarea.focus( ->
        clickMarkup($(this).closest(".comment"),null,$(this))
    )
    $textarea.focus()
    addToAddedCommentIDs(comment_id)
    console.log(removed_comment_ids)
    console.log(added_comment_ids)
    History.do(new AddCommentMemento($markups,$comment))

  

  removeComment = ($markups,$comment)->
    History.do(new DeleteCommentMemento($markups,$comment))
    comment_id = $comment.attr("id").split("_")[1]
    addToRemovedCommentIDs(comment_id, $comment.attr("data-id"))
    console.log(removed_comment_ids)
    console.log(added_comment_ids)
    jsPlumb.removeAllEndpoints($comment)
    $comment.detach()

  applyMarkupHover = ($comment,$markup)->
    comment_id = getCommentID($comment,$markup)
    $("[data-range-id='#{comment_id}']").addClass("markup-hover")
    $comment = $("#comment_#{comment_id}")
    if $comment.size() > 0
      source = $("[data-range-id='#{comment_id}']")[0]
      $comment.addClass("markup-hover")
      $connect = jsPlumb.connect({
        source: source
        target: $comment
        anchors: ["TopCenter","TopCenter"]
      },commentConOptions)
      jsPlumb.draggable($comment)

  unapplyMarkupHover = ($comment,$markup)->

    comment_id = getCommentID($comment,$markup)
    if !selectedMarkup or !selectedMarkup.$comment.is($("#comment_#{comment_id}"))
      $("[data-range-id='#{comment_id}']").removeClass("markup-hover")
      $comment = $("#comment_#{comment_id}")
      if $comment.size() > 0
        $comment.removeClass("markup-hover")
        jsPlumb.removeAllEndpoints($comment)

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
      $connect = jsPlumb.connect({
        source: $markups
        target: $comment
        anchors: ["TopCenter","TopCenter"]
      },commentConOptions)
      jsPlumb.draggable($comment)
    selectedMarkup = {
      $comment : $comment
      $markups : $markups
      # $connect : $connect
    }

  unselectMarkup = ->
    if selectedMarkup
      if selectedMarkup.$comment.size() > 0
        selectedMarkup.$comment.removeClass("markup-selected").removeClass("markup-hover")
        jsPlumb.removeAllEndpoints(selectedMarkup.$comment)
      if selectedMarkup.$markups
        selectedMarkup.$markups.removeClass("markup-selected").removeClass("markup-hover")
      selectedMarkup = null

  addMarkup = (range) ->
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
    jsPlumb.removeAllEndpoints($comment) if $comment
    $markupsToDelete.each((index) ->
        markCssApplier.removeMarkup(this.parentNode,this.childNodes[0],this,removeMarkupMemento)
    )
    $comment.detach()
    if selectedMarkup and selectedMarkup.$markups.index($(markup)) >= 0
      selectedMarkup = null
    console.log(removed_markup_ids)
    console.log(added_markup_ids)
    History.endCompoundDo()
  
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
        jsPlumb.removeAllEndpoints(@comment.get(0))
        @comment.detach()
        comment_id = $(@comment).attr("id").split("_")[1]
        addToRemovedCommentIDs(comment_id, $(@comment).attr("data-id"))
      console.log(removed_comment_ids)
      console.log(added_comment_ids)
      new DeleteCommentMemento(@markups,@comment)


  class DeleteCommentMemento

    constructor: (@markups,@comment) ->

    restore: ->
      if(@comment.size() > 0)
        comment_id = $(@comment).attr("id").split("_")[1]
        addToAddedCommentIDs(comment_id,$(@comment).attr("data-id"))
        $("#comments").append(@comment)
        @comment.find("textarea").focus()
      console.log(removed_comment_ids)
      console.log(added_comment_ids)
      new AddCommentMemento(@markups,@comment)

  class AddOutlineMemento

    constructor: (@startParagraph, @endParagraph, @outline) ->

    restore: ->
      outline_id = @outline.attr("id").split("_")[1]
      jsPlumb.select({source:"outline_#{outline_id}"}).each((connection) ->
        if connection.endpoints[1].connections.length <= 1
          connection.endpoints[1].setVisible(false)
      )
      jsPlumb.removeAllEndpoints(@outline)
      @outline.detach()
      addToRemovedOutlineIDs(outline_id,@outline.attr("data-outline-id"))
      new RemoveOutlineMemento(@startParagraph, @endParagraph, @outline)

  class RemoveOutlineMemento

    constructor: (@startParagraph, @endParagraph, @outline) ->

    restore: ->
      $("#comments").append(@outline)
      @outline.find("textarea").focus()
      outline_id = @outline.attr("id").split("_")[1]
      connectParagraphToOutline(@startParagraph, @endParagraph, @outline)
      addToAddedOutlineIDs(outline_id, @outline.attr("data-outline-id"))
      new AddOutlineMemento(@startParagraph, @endParagraph, @outline)


  $("#users").height($(window).height()-30)
  $(window).resize( ->
    $("#users").height($(window).height()-30)
  )

  window.onbeforeunload = () -> 
      console.log added_markup_ids
      console.log removed_markup_ids
      console.log added_comment_ids
      console.log removed_comment_ids
      console.log added_outline_ids
      console.log removed_outline_ids
      if removed_markup_ids.length > 0 or added_markup_ids.length > 0 or 
          added_comment_ids.length > 0 or removed_comment_ids.length > 0 or 
          added_outline_ids.length > 0 or removed_outline_ids.length > 0

        return "您所作的變更尚未儲存！！若離開將會失去你所做的變更！！";
    
  
  removeCursiveFont = () ->
    $("font").each( ->
        if $(this).attr("face") and $(this).attr("face").indexOf("cursive") > -1
          $(this).attr("face",$(this).attr("face").split(',').filter( (face) -> face.indexOf("cursive") == -1 ).toString())
    )
  
  restoreAfterLoad = ->
    
    removeCursiveFont()
    reMarkupAfterLoaded()
    reAttachCommentsAfterLoaded()
    reAttachOutlinesAfterLoaded()
    addVideoQtip()

  removeOutline = ($startParagraph, $endParagraph, $outline) ->
    outline_id = $outline.attr("id").split("_")[1]
    jsPlumb.select({source:"outline_#{outline_id}"}).each((connection) ->
      if connection.endpoints[1].connections.length <= 1
        connection.endpoints[1].setVisible(false)
    )
    jsPlumb.removeAllEndpoints($outline)
    addToRemovedOutlineIDs(outline_id, $outline.attr("data-outline-id"))
    $outline.detach()
    History.do(new RemoveOutlineMemento($startParagraph,$endParagraph,$outline))

  addOutline = (range)->
    $startParagraph = $(range.startContainer).closest("p")
    $endParagraph = $(range.endContainer).closest("p")
    midpointInParagraphs = ($startParagraph.position().top + $endParagraph.position().top + $endParagraph.height())/2
    $outline = $("<div>", {id : "outline_#{current_outline_id}"}).addClass("outline")
    outline_id = $outline.attr("id").split("_")[1]
    $closeButton = $("<img>",{src : "/assets/close.png"}).addClass("close-button").click(
      ->
        removeOutline($startParagraph, $endParagraph, $outline)
    )
    $outline.append($closeButton)

    $textarea = $('<textarea>')
    $textarea.autosize({append: "\n"})
    $textarea.width(180)
    $textarea.height(40)
    $textarea.on({
      input : addToAddedOutlineIDs(outline_id, $outline.attr("data-outline-id"))
      resize : addToAddedOutlineIDs(outline_id, $outline.attr("data-outline-id"))
      focus : ->        
        this.hasFocus = true
        $outline.addClass('outline-hover')
        jsPlumb.select({source:$outline.attr('id')}).each(
          (connection) ->
            connection.setHover(true)
        )
      focusout : ->
        this.hasFocus = false
        $outline.removeClass('outline-hover')
        jsPlumb.select({source:$outline.attr('id')}).each(
          (connection) ->
            connection.setHover(false)
        )
    })
    $outline.on({
          mousedown : ->
            this.mouseDownPosition = $(this).position()
          mouseup: ->
            if this.mouseDownPosition
              if $(this).position().top != this.mouseDownPosition.top or $(this).position().left != this.mouseDownPosition.left
                addToAddedOutlineIDs(outline_id, $outline.attr("data-outline-id"))
        })
    $outline.append($textarea)
    $("#comments").append($outline)
    console.log $outline.height()
    $outline.css({top: "#{midpointInParagraphs - $('#doc').position().top}px", left:"0px", position : 'absolute'})
    $textarea.focus()
    connectParagraphToOutline($startParagraph, $endParagraph, $outline)
    addToAddedOutlineIDs(outline_id)
    History.do(new AddOutlineMemento($startParagraph, $endParagraph, $outline))
    current_outline_id++

  $("#save-btn").click( ->
      save()
  )

  $("#mark-btn").click( ->
    edit_mode = MARKUP_MODE
  )

  $("#outline-btn").click( ->
    edit_mode = OUTLINE_MODE
  )

  $("#doc").bind 'mouseup', (e) ->
    
      selection = rangy.getSelection()
      unless selection.collapsed
        range = selection.getRangeAt(0)
        unless range.collapsed
          if edit_mode == MARKUP_MODE
            selection.removeAllRanges()
            addMarkup(range)
          else if edit_mode == OUTLINE_MODE
            selection.removeAllRanges()
            addOutline(range)

  $("#nav").find("a").addClass("unselectable").on( "onselectstart" , ->
        false
  )

  $(document).on(
    {
      keydown: (e) -> 
        if (e.metaKey and e.keyCode == 90)   
          History.undo(undoThreshold)
        if (e.metaKey and e.keyCode == 89)   
          e.preventDefault()
          History.redo()
        if (e.metaKey and e.keyCode == 83)   
          e.preventDefault()
          save()

    }
    
  )

  $("#undo-btn").click ->
      History.undo(undoThreshold)

  $("#redo-btn").click ->
      History.redo()

  $("body").on('click',":not(.markup *)", (e) ->
      e.stopPropagation()
      console.log this
      if $(this).closest(".comment").size() == 0 and $(this).closest(".markup").size() == 0  and $(this).closest(".outline").size() == 0 
        unselectMarkup()
  )

  $("#content").on(
    {
      mouseenter: ->
        $(this).addClass('outline-hover')
        jsPlumb.select({source:$(this).attr('id')}).each(
          (connection) ->
            connection.setHover(true)
        )
      mouseleave: ->
        if !$(this).find("textarea").get(0).hasFocus
          $(this).removeClass('outline-hover')
          jsPlumb.select({source:$(this).attr('id')}).each(
            (connection) ->
              connection.setHover(false)
          )
    }
    '.outline'
    )

  $("#content").on(
    {
      mouseenter: ->
        applyMarkupHover(null,$(this))

      mouseleave: ->
        unapplyMarkupHover(null,$(this))

      mousedown: (e) ->
        clickMarkup(null,$(this),null)
        if e.which == 1
          e.preventDefault()
    }
    ".markup"
  )

  $("#content").on(
    {
      mouseenter: ->
        applyMarkupHover($(this))

      mouseleave: ->
        unapplyMarkupHover($(this))

      mousedown: (e) ->
        this.downPosition = $(this).position()

      mouseup: (e) ->
        if this.downPosition and this.downPosition.top == $(this).position().top and this.downPosition.left == $(this).position().left
          clickMarkup($(this),null,$(e.target))

    }
    ".comment"
  )

  $("#users").on(
    {
      click:->
        user_id = $(this).attr("data-user-id")
        while(History.undo())
          ;
         $.ajax({
            url : 'load'
            type : 'POST'
            data: {
              user_id : user_id
                  }
            error: (xhr) ->
            success: (response) ->
              added_markup_ids.length = 0
              removed_markup_ids.length = 0
              added_comment_ids.length = 0
              removed_comment_ids.length = 0
              if(response['last_markup_id'])
                last_markup_id = parseInt(response['last_markup_id'])
              if(response['textRanges'])
                $("#text_ranges").text(response['textRanges'])
              if(response['comments'])
                $("#saved_comments").text(response['comments'])
              if(response['last_saved_outline_id'])
                $("#last_saved_outline_id").text(response['last_saved_outline_id'])
              if(response['outlines'])
                $("#outlines").text(response['outlines'])

        })
        restoreAfterLoad()
    }
    ".user"
  )
  
  restoreAfterLoad()
$ ->
  
  rangy.init();
  
  markup_id = 1
  markCssApplier = rangy.createCssClassApplier("markup", {normalize: false}, ["p","b","span","strong","a"]);
  
  jsPlumb.Defaults.Container = $("body");
  jsPlumb.importDefaults({
        Connector:"Flowchart",
        PaintStyle:{ lineWidth:3, strokeStyle:"#ffa500", "dashstyle":"2 4" },
        Endpoint:[ "Dot", { radius:5 } ],
        EndpointStyle:{ fillStyle:"#ffa500"
         }
        ConnectionsDetachable:false
      });
  isRangeStartAndEndInMarkup = (range) ->
    $(range.startContainer).parents().hasClass("markup") && $(range.endContainer).parents().hasClass("markup")
  
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
    markup_ids = $(elem).attr("data-range-id").split(" ")
    last_markup_id = markup_ids[markup_ids.length - 1]
    $comment = $('<textarea>',{id : "comment_#{last_markup_id}"})
    $("#comments").append($comment)
    $comment.autosize({append: "\n"});
    $markups = $(":regex(data-range-id, ( )*#{last_markup_id}( )* )")
    $comment.align({top:":regex(data-range-id, ( )*#{last_markup_id}( )* )"})
    $connect = jsPlumb.connect({
      source: $markups[0]
      target: $comment
      anchors: ["TopCenter","TopCenter"]
    })
    $connect.setVisible(false)
    $comment.qtip({
      content: $('<a>Delete</a>',{href : "#"}).click ->
          History.do(new DeleteCommentMemento($markups,$comment,$connect)) 
          jsPlumb.deleteEndpoint($connect.endpoints[0])
          $comment.remove()        
      position: {
        corner: {
          target: 'bottomRight',
          tooltip: 'topRight'
        }
      }
      hide: { when: 'mouseout', fixed: true }
    })
    $comment.focus()
    History.do(new AddCommentMemento($markups,$comment,$connect))
  $("#doc").bind 'mouseup', (e) ->
    selection = rangy.getSelection()
    range = selection.getRangeAt(0)
    unless range.collapsed
      selection.removeAllRanges()
      commonAncestor = if range.commonAncestorContainer.nodeType == 3 then range.commonAncestorContainer.parentNode else range.commonAncestorContainer
      History.do(new MarkupMemento(rangy.serializePosition(commonAncestor,0,$("#doc")[0]),(new XMLSerializer()).serializeToString(commonAncestor)))
      markCssApplier.applyToRange(range,markup_id)
      markup_id++
  
  $("#nav").find("a").addClass("unselectable").on( "onselectstart" , ->
        false
  )
          
  $("body").on(
    {
      mouseenter: ->
        markup_ids = $(this).attr("data-range-id").split(" ")
        if markup_ids.length > 0
          last_markup_id = markup_ids[markup_ids.length - 1]
        $(":regex(data-range-id, ( )*#{last_markup_id}( )* )").addClass("markup-selected")
        $("#comment_#{last_markup_id}").addClass("markup-selected")
        if jsPlumb.select({target : "comment_#{last_markup_id}"}).get(0)
          jsPlumb.select({target : "comment_#{last_markup_id}"}).get(0).setVisible(true)
             
      mouseleave: ->
        markup_ids = $(this).attr("data-range-id").split(" ")
        if markup_ids.length > 0
          last_markup_id = markup_ids[markup_ids.length - 1]
        $(":regex(data-range-id, ( )*#{last_markup_id}( )* )").removeClass("markup-selected")
        $("#comment_#{last_markup_id}").removeClass("markup-selected")
        if jsPlumb.select({target : "comment_#{last_markup_id}"}).get(0)  
          jsPlumb.select({target : "comment_#{last_markup_id}"}).get(0).setVisible(false)
    }
    ".markup"
  )
  
  $("body").on(
    {
      mouseenter: ->
        comment_id = $(this).attr("id")
        markups_id = comment_id.split("_")[1]
        $(":regex(data-range-id, ( )*#{markups_id}( )* )").addClass("markup-selected")
        $(this).addClass("markup-selected")
        jsPlumb.select({target : "#{comment_id}"}).get(0).setVisible(true)
        
      mouseleave: ->
        comment_id = $(this).attr("id")
        markups_id = comment_id.split("_")[1]
        $(":regex(data-range-id, ( )*#{markups_id}( )* )").removeClass("markup-selected")
        $(this).removeClass("markup-selected")
        jsPlumb.select({target : "#{comment_id}"}).get(0).setVisible(false)
    }
    "textarea"
  )
  
  deleteMarkup = (markup) ->
    markup_ids = $(markup).attr("data-range-id").split " "
    last_markup_id = markup_ids[markup_ids.length - 1]
    $markupsToDelete = $(":regex(data-range-id, ( )*#{last_markup_id}( )* )")
    commonAncestor = getCommonAncestor($markupsToDelete[0],$markupsToDelete[$markupsToDelete.length - 1])
    History.beginCompoundDo()
    History.do(new MarkupMemento(rangy.serializePosition(commonAncestor,0,$("#doc")[0]),(new XMLSerializer()).serializeToString(commonAncestor)))
    $markupsToDelete.each((index) ->
        markup_ids = $(this).attr("data-range-id").split(" ")
        if markup_ids.length <= 1   
          $(this).removeClass("markup").removeAttr("data-range-id")
        else
          last_markup_id_index = markup_ids.indexOf("#{last_markup_id}")
          if last_markup_id_index > -1
            markup_ids.splice(last_markup_id_index,1)
          $(this).attr("data-range-id",markup_ids.join(" "))
    )
    $comment = $("#comment_#{last_markup_id}")
    $connect = jsPlumb.select({target : "comment_#{last_markup_id}"}).get(0)
    jsPlumb.deleteEndpoint($connect.endpoints[0])
    History.do(new DeleteCommentMemento($markupsToDelete,$comment,$connect)) 
    $comment.remove() 
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
      jsPlumb.deleteEndpoint(@connect.endpoints[0])
      @comment.remove()
      new DeleteCommentMemento(@markups,@comment,@connect)
            
      
  class DeleteCommentMemento
    
    constructor: (@markups,@comment,@connect) ->
    
    restore: ->
      $comment = @comment
      $connect = @connect
      $markups = @markups
      $("#comments").append($comment)
      $connect = jsPlumb.connect({
        source: $markups[0]
        target: $comment
        anchors: ["TopCenter","TopCenter"]
      })
      $comment.qtip({
        content: $('<a>Delete</a>',{href : "#"}).click ->
          History.do(new DeleteCommentMemento($markups,$comment,$connect)) 
          jsPlumb.deleteEndpoint($connect.endpoints[0])
          $comment.remove()
        position: {
          corner: {
            target: 'bottomRight'
            tooltip: 'topRight'
          }
        }
      hide: { when: 'mouseout', fixed: true }
      })
      new AddCommentMemento($markups,$comment,$connect)
      
  class MarkupMemento
    
    constructor: (@node , @xml) ->
    
    restore: ->
      state = new MarkupMemento(@node, (new XMLSerializer()).serializeToString(rangy.deserializePosition(@node,$("#doc")[0]).node) )
      $(rangy.deserializePosition(@node,$("#doc")[0]).node).replaceWith(@xml)
      state

  class CompoundMemento
    
    _mementos : []
    
    push: (m) ->
      @_mementos.push(m)
      
    restore: ->
      inverse = new CompoundMemento()
      inverse.push(m.restore()) for m in @_mementos
      inverse
  
  class History
    
    @_isUndoRedo = false
    @_undoStack = []
    @_redoStack = []
    @_tempMemento = null
    
    @undo: ->
      if (@_tempMemento != null)
        throw "The complex memento wasn't commited."
      @_isUndoRedo = true
      @_redoStack.push(@_undoStack.pop().restore())
      @_isUndoRedo = false
      
    @redo: ->
      if (@_tempMemento != null)
        throw "The complex memento wasn't commited."
      @_isUndoRedo = true
      @_undoStack.push(@_redoStack.pop().restore())
      @_isUndoRedo = false
      
    @do: (m) ->
      if(@_isUndoRedo)
        throw "Involking do within an undo/redo action.!"
      if(@_tempMemento)
        @_tempMemento.push(m)
      else
        @_do(m)
    
    @_do: (m) ->
      @_redoStack.length = 0           
      @_undoStack.push m
    
    @beginCompoundDo: ->
      if (@_tempMemento != null)
        throw "Previous complex memento wasn't commited."
      @_tempMemento = new CompoundMemento();
      
    @endCompoundDo: ->
      if (@_tempMemento == null)
        throw "Ending a non-existing complex memento"
      @_do(@_tempMemento);
      @_tempMemento = null;
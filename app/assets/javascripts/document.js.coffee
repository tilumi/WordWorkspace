$ ->
  rangy.init();
  undoStates = []
  redoStates = []
  
  docElem = $("#doc")[0]
  markup_id = 1
  markCssApplier = rangy.createCssClassApplier("markup", {normalize: false}, ["p","b","span","strong","a"]);
  
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
    @markup_id = $(elem).attr("data-range-id")
    $comment = $('<textarea>',{id : "comment_#{@markup_id}"})
    $("#comments").append($comment)
    $comment.autosize({append: "\n"});
    $comment.align({top:"*[data-range-id = '#{@markup_id}']"})
    $comment.qtip({
      content: '<a href="#" >Delete</a> <a href="#">Reply<a/>',
      position: {
        corner: {
          target: 'bottomRight',
          tooltip: 'topRight'
        }
      }
      hide: { when: 'mouseout', fixed: true }
    })
    $comment.focus()
    History.do(new AddCommentMemento($comment))
  deleteComment = ($comment) ->
    History.do(new DeleteCommentMemento($comment))
    $comment.remove()  
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
          
  $("body").layout({ applyDefaultStyles: true })
  
  getTextInRange = (range) ->
    range.getNodes([3]).map( (e) -> e.data ).reduce( (n1,n2) -> n1 + n2 ).replace(/(\r\n|\n|\r)/gm,""); 
      
  $("body").on(
    {
      mouseenter: ->
        @markup_id = $(this).attr("data-range-id")
        $("*[data-range-id = '#{@markup_id}']").addClass("markup-selected")
        $("#comment_#{@markup_id}").addClass("markup-selected")
             
      mouseleave: ->
        @markup_id = $(this).attr("data-range-id")
        $("*[data-range-id = '#{@markup_id}']").removeClass("markup-selected")
        $("#comment_#{@markup_id}").removeClass("markup-selected")  
    }
    ".markup"
  )
  
  deleteMarkup = (markup) ->
    @markup_id = $(markup).attr("data-range-id")
    markupsToDelete = $("span[data-range-id = '#{@markup_id}']")
    commonAncestor = getCommonAncestor(markupsToDelete[0],markupsToDelete[markupsToDelete.length - 1])
    History.do(new MarkupMemento(rangy.serializePosition(commonAncestor,0,$("#doc")[0]),(new XMLSerializer()).serializeToString(commonAncestor)))
    markupsToDelete.removeClass("markup").removeAttr("data-range-id")

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
    
    constructor: (@comment) -> 
    
    restore: ->
      @comment.remove()
      new DeleteCommentMemento(@comment)
            
      
  class DeleteCommentMemento
    
    constructor: (@comment) ->
      
    restore: ->
      $("#comments").append(@comment)
      new AddCommentMemento(@comment)
      
  class MarkupMemento
    
    constructor: (@node , @xml) ->
    
    restore: ->
      state = new MarkupMemento(@node, (new XMLSerializer()).serializeToString(rangy.deserializePosition(@node,$("#doc")[0]).node) )
      $(rangy.deserializePosition(@node,$("#doc")[0]).node).replaceWith(@xml)
      state

  class History
    
    @_isUndoRedo = false
    @_undoStack = []
    @_redoStack = []
    
    @undo: ->
      @_isUndoRedo = true
      @_redoStack.push(@_undoStack.pop().restore())
      @_isUndoRedo = false
      
    @redo: ->
      @_isUndoRedo = true
      @_undoStack.push(@_redoStack.pop().restore())
      @_isUndoRedo = false
      
     @do: (m) ->
       if(@_isUndoRedo)
          console.log("Involking do within an undo/redo action.!")
       @_redoStack.length = 0           
       @_undoStack.push m
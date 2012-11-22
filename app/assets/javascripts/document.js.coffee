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
        ,
        items: {
          "edit": {name: "Edit", icon: "edit"},
          "cut": {name: "Cut", icon: "cut"},
          "copy": {name: "Copy", icon: "copy"},
          "paste": {name: "Paste", icon: "paste"},
          "delete": {name: "Delete", icon: "delete"},
          "sep1": "---------",
          "quit": {name: "Quit", icon: "quit"}
        }
    }
  )
  
  $("#doc").bind 'mouseup', (e) ->       
    selection = rangy.getSelection()
    range = selection.getRangeAt(0)
    unless range.collapsed
      selection.removeAllRanges()
      commonAncestor = if range.commonAncestorContainer.nodeType == 3 then range.commonAncestorContainer.parentNode else range.commonAncestorContainer
      History.do(new AddMarkupMemento(rangy.serializePosition(commonAncestor,0,$("#doc")[0]),(new XMLSerializer()).serializeToString(commonAncestor)))
      markCssApplier.applyToRange(range,markup_id)
      markup_id++
      
      
  $("body").layout({applyDefaultStyles: true})
  
  getTextInRange = (range) ->
    range.getNodes([3]).map( (e) -> e.data ).reduce( (n1,n2) -> n1 + n2 ).replace(/(\r\n|\n|\r)/gm,""); 
      
  $("body").on(
    {
      mouseenter: ->
        @markup_id = $(this).attr("data-range-id")
        $("span[data-range-id = '#{@markup_id}']").addClass("markup-selected")
             
      mouseleave: ->
        @markup_id = $(this).attr("data-range-id")
        $("span[data-range-id = '#{@markup_id}']").removeClass("markup-selected")  
    }
    ".markup"
  )
  
  deleteMarkup = (markup) ->
    @markup_id = $(markup).attr("data-range-id")
    markupsToDelete = $("span[data-range-id = '#{@markup_id}']")
    commonAncestor = getCommonAncestor(markupsToDelete[0],markupsToDelete[markupsToDelete.length - 1])
    History.do(new AddMarkupMemento(rangy.serializePosition(commonAncestor,0,$("#doc")[0]),(new XMLSerializer()).serializeToString(commonAncestor)))
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
    
  class AddMarkupMemento
    
    constructor: (@node , @xml) ->
    
    restore: ->
      state = new AddMarkupMemento(@node, (new XMLSerializer()).serializeToString(rangy.deserializePosition(@node,$("#doc")[0]).node) )
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
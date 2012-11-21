$ ->
  rangy.init();
  markups = []
  docElem = $("#doc")[0]
  markup_id = 1
  markCssApplier = rangy.createCssClassApplier("markup", {normalize: false}, ["p","b","span","strong","a"]);
  
  isRangeStartAndEndInMarkup = (range) ->
    $(range.startContainer).parents().hasClass("markup") && $(range.endContainer).parents().hasClass("markup")

  $("body").bind 'mouseup', (e) ->       
    selection = rangy.getSelection()
    range = selection.getRangeAt(0)
    unless range.collapsed
      selection.removeAllRanges()
      # unless isRangeStartAndEndInMarkup(range)
      selectedText = getTextInRange(range)
      markCssApplier.applyToRange(range,markup_id)
      markups.push range.getNodes([3],(node )->
        node.data.indexOf("\n") == -1
      )
      markup_id++;
      
      
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
  

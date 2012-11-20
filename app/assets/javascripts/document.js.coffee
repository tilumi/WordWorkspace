$ ->
  rangy.init();
  markups = []
  docElem = $("#doc")[0]
  markup_id = 1
  markCssApplier = rangy.createCssClassApplier("markup", {normalize: true}, ["p","b","span","strong","a"]);
  
  $("body").bind 'mouseup', (e) ->       
    selection = rangy.getSelection()
    range = selection.getRangeAt(0)
    unless range.collapsed
      selectedText = getTextInRange(range)
      markCssApplier.applyToRange(range,markup_id)
      markups.push range.getNodes([3],(node )->
        node.data.indexOf("\n") == -1
        )
      selection.removeAllRanges()
      markup_id++;
      console.log(markups)
      
      
  $("body").layout({applyDefaultStyles: true})
  
  getTextInRange = (range) ->
    range.getNodes([3]).map( (e) -> e.data ).reduce( (n1,n2) -> n1 + n2 ).replace(/(\r\n|\n|\r)/gm,""); 
      
  $("body").on(
    {
      mouseenter: ->
        @markup_id = $(this).attr("data-range-id")
        for markup in markups
          if $(markup).parent().attr("data-range-id") == @markup_id
            $(markup).parent().addClass("markup-selected")
            break;
             
      mouseleave: ->
        for markup in markups
          for node in markup 
            $(node).parent().removeClass("markup-selected")   
    }
    ".markup"
  )    
  

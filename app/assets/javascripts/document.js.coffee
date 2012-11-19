$ ->
  rangy.init();
  markups = []
  markCssApplier = rangy.createCssClassApplier("markup", {normalize: true}, ["p","b","span","strong","a"]);
  docElem = $("#doc")[0]
  
  $("body").bind 'mouseup', (e) ->       
    selection = rangy.getSelection()
    unless selection.getRangeAt(0).collapsed
      markups.push rangy.serializeSelection(selection,true,docElem)
      selectedText = getTextInRange(selection.getRangeAt(0))
      try
        markCssApplier.applyToRange(selection.getRangeAt(0))
      catch error
        console.log error
      console.log rangy.deserializeRange(markups[markups.length - 1],docElem).getNodes(
        [1]
        (node) ->
          /\bmarkup\b/.test $(node).attr("class")
      )
      selection.removeAllRanges()
      console.log(markups)
      
  $("body").layout({applyDefaultStyles: true})
  
  getTextInRange = (range) ->
    range.getNodes([3]).map( (e) -> e.data ).reduce( (n1,n2) -> n1 + n2 ).replace(/(\r\n|\n|\r)/gm,""); 
      
  $("body").on(
    {
      mouseenter: ->
        $(this).addClass("markup-selected")
      mouseleave: ->
        $(this).removeClass("markup-selected")    
    }
    ".markup"
  )    
  

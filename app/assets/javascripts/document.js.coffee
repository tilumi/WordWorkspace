$ ->
  rangy.init();
  markups = []
  markCssApplier = rangy.createCssClassApplier("markup", {normalize: true}, ["p","b","span","strong","a"]);
  docElem = $("#doc")[0]
  
  $("body").bind 'mouseup', (e) ->       
    selection = rangy.getSelection()
    range = selection.getRangeAt(0)
    unless range.collapsed
      selectedText = getTextInRange(range)
      markCssApplier.applyToRange(range)
      markups.push range.getNodes([3])
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
  

$ ->
  rangy.init();
  markups = []
  markCssApplier = rangy.createCssClassApplier("markup", {normalize: true}, ["p","b","span","strong","a"]);
  
  $("body").bind 'mouseup', (e) ->       
    selection = rangy.getSelection()
    unless selection.getRangeAt(0).collapsed
      markups.push rangy.serializeSelection(selection,true)
      markCssApplier.applyToRange(selection.getRangeAt(0))
      selection.removeAllRanges()
      console.log(markups)
   $("body").layout({applyDefaultStyles: true})
   
  

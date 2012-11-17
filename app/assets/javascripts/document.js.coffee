$ ->
  getTextNodeByCoordinates = (elemCoordinates) ->
    elem = $("html")
    for elemCoordinate in elemCoordinates
      elem = $(elem).contents().get(elemCoordinate)
    elem
     
  getSelectionByCoordinates = (startElemCoordinates,endElemCoordinates) ->
    range = rangy.createRange()
    startOffset = startElemCoordinates.pop()
    endOffset = endElemCoordinates.pop()
    range.setStart(getTextNodeByCoordinates(startElemCoordinates),startOffset)
    range.setEnd(getTextNodeByCoordinates(endElemCoordinates),endOffset)
    range
     
  getPositionInDom = (e,offset) ->
    result = [] 
    while $(e).parent().get(0)
      result.push $($(e).parent().get(0)).contents().index(e)
      e = $(e).parent().get(0)
    result.reverse()
    result.shift()
    result.push offset
    result
  
  $("body").bind 'mouseup', (e) -> 
    selection = rangy.getSelection()
    range = selection.getRangeAt(0)
    unless range.collapsed
      selectedTextNodes = getSelectionByCoordinates( getPositionInDom(range.startContainer,range.startOffset), getPositionInDom(range.endContainer,range.endOffset) ).getNodes([3])
      firstTextNode = selectedTextNodes.shift()
      firstTextNodeSelection = rangy.createRange()
      firstTextNodeSelection.setStart(firstTextNode,range.startOffset)
      firstTextNodeSelection.setEnd(firstTextNode,firstTextNode.data.length)
      lastTextNode = selectedTextNodes.pop()
      lastTextNodeSelection = rangy.createRange()
      lastTextNodeSelection.setStart(lastTextNode,0)
      lastTextNodeSelection.setEnd(lastTextNode,range.endOffset)
      span = document.createElement("span");
      $(span).addClass("markup")
      firstTextNodeSelection.surroundContents(span)
      span = document.createElement("span");
      $(span).addClass("markup")
      lastTextNodeSelection.surroundContents(span)
      $(selectedTextNodes).wrap("<span class='markup'>")
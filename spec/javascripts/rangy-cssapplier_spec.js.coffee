describe "Rangy css applier", ->

  it "Text node equality", ->
    expect(document.createTextNode('123').nodeValue).toEqual(document.createTextNode('123').nodeValue)

  it "Add markup on complex html can be undo", ->
    loadFixtures "complex_html"
    History.init()
    History.beginCompoundDo()
    cssApplier = rangy.createCssClassApplier("markup",null, ["p","b","span","strong","a","font"]);
    cssApplier.applyToTextNode(textNode) for textNode in getTextNodesIn($("#1")[0],true)
    History.endCompoundDo()
    expect(textNode.parentNode).toHaveClass("markup") for textNode in getTextNodesIn($("#1")[0],true)
    History.undo()
    expect(textNode.parentNode).not.toHaveClass("markup") for textNode in getTextNodesIn($("#1")[0],true)

describe "Rangy css applier add range ID", ->

  beforeEach ->
        History.init()
        loadFixtures "addRangeID/complex_html"
        rangy.init();
        @range = rangy.createRange()
        @range.setStart($("#start")[0].childNodes[0] ,3)
        @range.setEnd($("#end")[0].childNodes[0],3)
        @cssApplier = rangy.createCssClassApplier("markup",null, ["p","b","span","strong","a","font"]);

  it "Add markup on all text nodes' parents", ->
    @cssApplier.applyToRange(@range,1)
    textNodes = @range.getNodes([3])
    expect(textNode.parentNode).toHaveClass("markup") for textNode in textNodes

  it "Add range ID on all text nodes' parents", ->

    @cssApplier.applyToRange(@range,1)

    textNodes = @range.getNodes([3])
    expect(textNode.parentNode).toHaveAttr("data-range-id",1) for textNode in textNodes
    expect($("#start").contents().length).toEqual(2)
    expect($("#end").contents().length).toEqual(2)

  it "Add multiple range ID on same ranges", ->
    @cssApplier.applyToRange(@range,1)
    @cssApplier.applyToRange(@range,2)

    textNodes = @range.getNodes([3])
    expect(textNode.parentNode).toHaveAttr("data-range-id","1 2") for textNode in textNodes
    expect($("#start").contents().length).toEqual(2)
    expect($("#end").contents().length).toEqual(2)

  it "Add range ID on same ranges can be undo and redo", ->
    History.beginCompoundDo()
    @cssApplier.applyToRange(@range,1)
    History.endCompoundDo()
    History.beginCompoundDo()
    @cssApplier.applyToRange(@range,2)
    History.endCompoundDo()

    textNodes = @range.getNodes([3])
    expect(textNode.parentNode).toHaveAttr("data-range-id","1 2") for textNode in textNodes
    History.undo()
    expect(textNode.parentNode).toHaveAttr("data-range-id","1") for textNode in textNodes
    History.redo()
    expect(textNode.parentNode).toHaveAttr("data-range-id","1 2") for textNode in textNodes

  it "Add range ID on different ranges", ->

    @cssApplier.applyToRange(@range,1)
    another_range = rangy.createRange()
    another_range.setStart($("#start")[0].childNodes[0],1)
    another_range.setEnd($("#end")[0].childNodes[1],3)
    @cssApplier.applyToRange(another_range,2)
    expect($(textNode.parentNode).attr("data-range-id").split(" ")[0]).toEqual("1") for textNode in @range.getNodes([3])
    expect($(textNode.parentNode).attr("data-range-id").split(" ").pop()).toEqual("2") for textNode in another_range.getNodes([3])

  it "Add range ID on different ranges that have partial common area can be undo", ->

    this.addMatchers({
        toEqualNode: (node) ->
          result = (this.actual == node)
          this.message = ->
            "expect " + $('<div>').append($(this.actual).clone()).html() + "  to equal " + $('<div>').append($(node).clone()).html();
          result

    });
    htmlStr_initial = $("#root").html()
    $allDoms_initial = $("#root").find("*")
    History.beginCompoundDo()
    @cssApplier.applyToRange(@range,1)
    History.endCompoundDo()

    htmlStr_afterFirstMarkup = $("#root").html()
    $allDoms_afterFirstMarkup = $("#root").find("*")

    expect($(textNode.parentNode).attr("data-range-id").split(" ")[0]).toEqual("1") for textNode in @range.getNodes([3])

    another_range = rangy.createRange()
    another_range.setStart($("#start")[0].childNodes[0],1)
    another_range.setEnd($("#end")[0].childNodes[0].childNodes[0],1)

    History.beginCompoundDo()
    @cssApplier.applyToRange(another_range,2)
    History.endCompoundDo()
    htmlStr_afterSecondMarkup = $("#root").html()
    $allDoms_afterSecondMarkup = $("#root").find("*")

    expect($(textNode.parentNode).attr("data-range-id").split(" ").pop()).toEqual("2") for textNode in another_range.getNodes([3])
    expect(htmlStr_afterSecondMarkup).not.toEqual(htmlStr_afterFirstMarkup)

    History.undo()

    expect($("#root").html()).toEqual(htmlStr_afterFirstMarkup)
    $("#root").find("*").each (index,element)->
        expect(element).toEqual($allDoms_afterFirstMarkup[index])
    htmlStr_temp = $("#root").html()

    History.undo()

    expect($("#root").html()).toEqual(htmlStr_initial)
    $("#root").find("*").each (index,element)->
        expect(element).toEqual($allDoms_initial[index])

    History.redo()

    expect($("#root").html()).toEqual(htmlStr_afterFirstMarkup)
    $("#root").find("*").each (index,element)->
        expect(element).toEqualNode($allDoms_afterFirstMarkup[index])

    History.redo()

    expect($("#root").html()).toEqual(htmlStr_afterSecondMarkup)
    $("#root").find("*").each (index,element)->
        expect(element).toEqualNode($allDoms_afterSecondMarkup[index])
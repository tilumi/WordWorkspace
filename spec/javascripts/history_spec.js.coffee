describe "History", ->
  it "Compound undo", ->
    class sampleMemento
      restore: ->

    History.beginCompoundDo()
    History.do(new sampleMemento())
    History.do(new sampleMemento())
    History.endCompoundDo()

    expect(History._undoStack.length).toEqual(1)
    expect(History._undoStack[0]._mementos.length).toEqual(2)
    memento1 = History._undoStack[0]._mementos[0]
    memento2 = History._undoStack[0]._mementos[1]
    spyOn(memento1,"restore").callThrough
    spyOn(memento2,"restore").callThrough

    History.undo()

    expect(memento1.restore.calls.length).toEqual(1)
    expect(memento2.restore.calls.length).toEqual(1)
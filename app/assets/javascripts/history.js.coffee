class History

    @_isUndoRedo = false
    @_undoStack = []
    @_redoStack = []
    @_tempMemento = null

    class CompoundMemento

        constructor: ->
          @_mementos = []

        push: (m) ->
          @_mementos.push(m)

        restore: ->
          inverse = new CompoundMemento()
          inverse.push(m.restore()) for m in @_mementos.reverse()
          inverse
    
    @init: ->
      @_isUndoRedo = false
      @_undoStack = []
      @_redoStack = []
      @_tempMemento = null

    @undo: ->
      if (@_tempMemento != null)
        throw "The complex memento wasn't commited."
      result = false  
      @_isUndoRedo = true
      if @_undoStack.length > 0
        @_redoStack.push(@_undoStack.pop().restore())
        result = true
      @_isUndoRedo = false
      result

    @redo: ->
      if (@_tempMemento != null)
        throw "The complex memento wasn't commited."
      @_isUndoRedo = true
      if @_redoStack.length > 0
        @_undoStack.push(@_redoStack.pop().restore())
      @_isUndoRedo = false

    @do: (m) ->
      if(@_isUndoRedo)
        throw "Involking do within an undo/redo action.!"
      if(@_tempMemento)
        @_tempMemento.push(m)
      else
        @_do(m)

    @_do: (m) ->
      @_redoStack.length = 0
      @_undoStack.push m

    @beginCompoundDo: ->
      if (@_tempMemento != null)
        throw "Previous complex memento wasn't commited."
      @_tempMemento = new CompoundMemento()

    @endCompoundDo: ->
      if (@_tempMemento == null)
        throw "Ending a non-existing complex memento"
      @_do(@_tempMemento);
      @_tempMemento = null;

window["History"] = History

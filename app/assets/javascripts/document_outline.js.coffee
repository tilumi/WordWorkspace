$ ->

	last_saved_outline_id = parseInt($("#last_saved_outline_id").text())
	current_outline_id = last_saved_outline_id + 1
	_added_outline_ids = []
	_removed_outline_ids = []
	originalId = null
	jsPlumb.Defaults.Container = $("body")

	outlinesConOptions = {
        connector : [ "Bezier", { curviness:100 } ]
        paintStyle:{ lineWidth:3, strokeStyle:"#ffa500", "dashstyle":"2 4" }
        cssClass : 'outline'
        hoverClass : 'outline-hover'
      }

  outlineEndpointOptions = {
            isSource:true
            endpoint:[ "Dot", { radius:5 } ],
            paintStyle:{ fillStyle:"#ffa500" }
          }

  dropOptions = {
        tolerance:'touch',
        hoverClass:'dropHover',
        activeClass:'dragActive',
        zIndex : 5,
      };

  setTimeout(() ->
    $("p").each( -> 

      bindEventsOnEndpoints = (endpoint, scope) ->
        endpoint.bind('mouseenter', (endpoint,event) ->
          jsPlumb.selectEndpoints({scope:scope}).each((endpoint) ->
              endpoint.setVisible(true)
          )
        )

        endpoint.bind('mouseexit', (endpointTarget,event) ->
          jsPlumb.selectEndpoints({scope:scope}).each((endpoint) ->
              if endpoint.connections.length == 0
                endpoint.setVisible(false)
          )
        )

      if($(this).text().length > 0 && /\S/gm.test($(this).text()))
        
        endpointStart = jsPlumb.addEndpoint(this,
          {
            isTarget:true
            anchor : [1,0,1,0]
            scope : 'outline-start'
            endpoint:[ "Dot", { radius:10, cssClass : 'outline', hoverClass : 'outline-target-hover' } ],
            paintStyle:{ fillStyle:"#ffa500" }
            dropOptions : dropOptions
            reattach : true
            maxConnections : -1
            beforeDrop: (params) ->
              History.do(new ChangeEndpointMemento(originalId, params.targetId,'outline-start',$("##{params.sourceId}")))
              addToAddedOutlineIDs(params.sourceId.split("_")[1],$("##{params.sourceId}").attr("data-outline-id"))
              true
            beforeDetach: (connection) ->
              originalId = connection.targetId
          })
        endpointStart.setVisible(false)

        endpointEnd = jsPlumb.addEndpoint(this,
          {
            isTarget:true
            anchor : [1,1,1,0]
            scope : 'outline-end'
            endpoint:[ "Dot", { radius:10, cssClass : 'outline', hoverClass : 'outline-target-hover' } ],
            paintStyle:{ fillStyle:"#ffa500" }
            dropOptions : dropOptions
            maxConnections : -1
            reattach : true
            beforeDrop: (params) ->
              History.do(new ChangeEndpointMemento(originalId, params.targetId,'outline-end',$("##{params.sourceId}")))
              addToAddedOutlineIDs(params.sourceId.split("_")[1],$("##{params.sourceId}").attr("data-outline-id"))
              true
            beforeDetach: (connection) ->
              originalId = connection.targetId
          })
        endpointEnd.setVisible(false)
        bindEventsOnEndpoints(endpointStart,'outline-start')
        bindEventsOnEndpoints(endpointEnd,'outline-end')
    )
  ,100)

	jsPlumb.bind('connectionDragStop', (connection) ->
      jsPlumb.selectEndpoints({scope:connection.endpoints[1].scope}).each((endpoint) ->
        if endpoint.connections.length == 0
            endpoint.setVisible(false)
      )
  )

	class ChangeEndpointMemento

	  constructor: (@originalId, @currentId, @scope, @outline)->

	  restore: ->
	    scopeAnchorHash = new Object()
	    scopeAnchorHash['outline-start'] = [ 0,0.4,-1,0]
	    scopeAnchorHash['outline-end'] = [ 0,0.6,-1,0]
	    jsPlumb.select({source: @outline.attr('id') ,scope: @scope}).each((connection) ->
	        if connection.endpoints[1].connections.length <= 1
	          connection.endpoints[1].setVisible(false)
	        jsPlumb.deleteEndpoint(connection.endpoints[0])

	      )
	    targetEndpoint = jsPlumb.selectEndpoints({scope:@scope,target:@originalId}).get(0)
	    targetEndpoint.setVisible(true)
	    sourceEndpoint = jsPlumb.addEndpoint(@outline,
	      {
	        anchor: scopeAnchorHash[@scope]
	      },outlineEndpointOptions)
	    sourceEndpoint.setDragAllowedWhenFull(false)
	    jsPlumb.connect(
	      {source: sourceEndpoint
	      target : targetEndpoint
	      scope : @scope},
	      outlinesConOptions
	      )
	    addToAddedOutlineIDs(@outline.attr("id").split("_")[1],@outline.attr("data-outline-id"))
	    new ChangeEndpointMemento(@currentId, @originalId, @scope, @outline)

	class AddOutlineMemento

	  constructor: (@startParagraph, @endParagraph, @outline) ->

	  restore: ->
	    outline_id = @outline.attr("id").split("_")[1]
	    jsPlumb.select({source:"outline_#{outline_id}"}).each((connection) ->
	      if connection.endpoints[1].connections.length <= 1
	        connection.endpoints[1].setVisible(false)
	    )
	    jsPlumb.removeAllEndpoints(@outline)
	    @outline.detach()
	    addToRemovedOutlineIDs(outline_id,@outline.attr("data-outline-id"))
	    new RemoveOutlineMemento(@startParagraph, @endParagraph, @outline)

	class RemoveOutlineMemento

    constructor: (@startParagraph, @endParagraph, @outline) ->

    restore: ->
      $("#comments").append(@outline)
      @outline.find("textarea").focus()
      outline_id = @outline.attr("id").split("_")[1]
      connectParagraphToOutline(@startParagraph, @endParagraph, @outline)
      addToAddedOutlineIDs(outline_id, @outline.attr("data-outline-id"))
      new AddOutlineMemento(@startParagraph, @endParagraph, @outline)

	addToAddedOutlineIDs = (oid, id) ->
    createAddToOutlineIDsFunc(_added_outline_ids,_removed_outline_ids).call(this, "#{oid}", "#{id}")

  addToRemovedOutlineIDs = (oid, id) ->
    createAddToOutlineIDsFunc(_removed_outline_ids,_added_outline_ids).call(this, "#{id}", "#{oid}")

  createAddToOutlineIDsFunc = (outline_ids_to_add, outline_ids_to_remove) ->
    (oid, id) ->
      if oid
        if outline_ids_to_add.indexOf(oid) == -1
          outline_ids_to_add.push(oid)
      if id
        if outline_ids_to_remove.indexOf(id) > -1
          outline_ids_to_remove.splice(outline_ids_to_remove.indexOf(id),1)	  

	createOutline = ($startParagraph, $endParagraph, saved_outline) ->

		removeOutline = ($startParagraph, $endParagraph, $outline) ->
	    outline_id = $outline.attr("id").split("_")[1]
	    jsPlumb.select({source:"outline_#{outline_id}"}).each((connection) ->
	      if connection.endpoints[1].connections.length <= 1
	        connection.endpoints[1].setVisible(false)
	    )
	    jsPlumb.removeAllEndpoints($outline)
	    addToRemovedOutlineIDs(outline_id, $outline.attr("data-outline-id"))
	    $outline.detach()
	    History.do(new RemoveOutlineMemento($startParagraph,$endParagraph,$outline))


    if saved_outline
      outline_id = saved_outline.oid
    else
      outline_id = current_outline_id

    if saved_outline
      $outline = $("<div>", {id : "outline_#{outline_id}"}).addClass(saved_outline.className)
    else
      $outline = $("<div>", {id : "outline_#{outline_id}"}).addClass('outline')

    if saved_outline
      $outline.attr("data-outline-id",saved_outline.id)
      $outline.css({top: "#{saved_outline.y}px", left: "#{saved_outline.x}px", position: "absolute"})
    else
      midpointInParagraphs = ($startParagraph.position().top + $endParagraph.position().top + $endParagraph.height())/2
      $outline.css({top: "#{midpointInParagraphs - $('#doc').position().top}px", left:"0px", position : 'absolute'})

    $textarea = $('<textarea>')
    $textarea.autosize({append: "\n"})
    if saved_outline
      $textarea.width(saved_outline.width)
      $textarea.height(saved_outline.height)
      $textarea.val(saved_outline.content)
    else
      $textarea.width(180)
      $textarea.height(40)

    $closeButton = $("<img>",{src : "/assets/close.png"}).addClass("close-button").click(
        ->
          removeOutline($startParagraph, $endParagraph, $outline)
    )
    $outline.append($closeButton)
    $outline.append($textarea)

    $textarea.on({
      input : addToAddedOutlineIDs(outline_id, $outline.attr("data-outline-id"))
      resize : addToAddedOutlineIDs(outline_id, $outline.attr("data-outline-id"))
      focus : ->        
        this.hasFocus = true
        $outline.addClass('outline-hover')
        jsPlumb.select({source:$outline.attr('id')}).each(
          (connection) ->
            connection.setHover(true)
        )
      focusout : ->
        this.hasFocus = false
        $outline.removeClass('outline-hover')
        jsPlumb.select({source:$outline.attr('id')}).each(
          (connection) ->
            connection.setHover(false)
        )
    })
    $outline.on({
      mousedown : ->
        this.mouseDownPosition = $(this).position()
      mouseup: ->
        if this.mouseDownPosition
          if $(this).position().top != this.mouseDownPosition.top or $(this).position().left != this.mouseDownPosition.left
            addToAddedOutlineIDs(outline_id, $outline.attr("data-outline-id"))
    })
    $outline

	connectParagraphToOutline = ($startParagraph, $endParagraph, $outline) ->
    startTargetEndpoint = jsPlumb.getEndpoints($startParagraph).filter((endpoint) -> endpoint.scope == "outline-start")[0]
    startTargetEndpoint.setVisible(true)
    startSourceEndpoint = jsPlumb.addEndpoint($outline,
      {
        anchor: [ 0,0.4,-1,0]
      },outlineEndpointOptions)
    startSourceEndpoint.setDragAllowedWhenFull(false)
    jsPlumb.connect(
      {source: startSourceEndpoint
      target : startTargetEndpoint
      scope : 'outline-start'},
      outlinesConOptions
      )
    endTargetEndpoint = jsPlumb.getEndpoints($endParagraph).filter((endpoint) -> endpoint.scope == "outline-end")[0]
    endTargetEndpoint.setVisible(true)
    endSourceEndpoint = jsPlumb.addEndpoint($outline,
      {
        anchor: [ 0,0.6,-1,0]
      },outlineEndpointOptions)
    endSourceEndpoint.setDragAllowedWhenFull(false)
    jsPlumb.connect(
      {source: endSourceEndpoint
      target : endTargetEndpoint
      scope : 'outline-end'},
      outlinesConOptions
    )
    jsPlumb.draggable($outline)

	class Outline
	  	
		@added_outline_ids = ->
			_added_outline_ids

		@removed_outline_ids = ->
			_removed_outline_ids

		@initAfterSave = -> 
			@initAfterReload()
			last_saved_outline_id = current_outline_id - 1

		@initAfterReload = ->
			_added_outline_ids.length = 0
			_removed_outline_ids.length = 0			

		@isDirty = ->      
			return _added_outline_ids.length > 0 or _removed_outline_ids.filter( (id) -> id != 'undefined' && id ).length > 0

		@addOutline = (range)->
	    $startParagraph = $(range.startContainer).closest("p")
	    $endParagraph = $(range.endContainer).closest("p")
	    $outline = createOutline($startParagraph, $endParagraph)
	    $("#comments").append($outline)
	    $outline.find('textarea').focus()
	    connectParagraphToOutline($startParagraph, $endParagraph, $outline)
	    addToAddedOutlineIDs($outline.attr("id").split("_")[1])
	    History.do(new AddOutlineMemento($startParagraph, $endParagraph, $outline))
	    current_outline_id++
	  
	  @reAttachOutlinesAfterLoaded = () ->
	    saved_outlines = JSON.parse($("#outlines").text())
	    for saved_outline in saved_outlines
	      do(saved_outline) ->
	        $startParagraph = $(rangy.deserializePosition(saved_outline.start_paragraph, $("#doc").get(0)).node)
	        $endParagraph = $(rangy.deserializePosition(saved_outline.end_paragraph, $("#doc").get(0)).node)
	        $outline = createOutline($startParagraph, $endParagraph, saved_outline)
	        $("#comments").append($outline)
	        setTimeout(() -> 
	            connectParagraphToOutline($startParagraph, $endParagraph, $outline)
	          ,100)
	        History.do(new AddOutlineMemento($startParagraph, $endParagraph, $outline))
	        _added_outline_ids.length = 0

	window.Outline = Outline
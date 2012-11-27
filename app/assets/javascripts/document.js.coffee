$ ->
  
  rangy.init();
  
  markup_id = 1
  markCssApplier = rangy.createCssClassApplier("markup", {normalize: false}, ["p","b","span","strong","a"]);
  
  jsPlumb.Defaults.Container = $("body");
  jsPlumb.importDefaults({
        Connector:[ "Flowchart", { stub:10 } ]
        PaintStyle:{ lineWidth:3, strokeStyle:"#ffa500", "dashstyle":"2 4" },
        Endpoint:[ "Dot", { radius:5 } ],
        EndpointStyle:{ fillStyle:"#ffa500"
         }
        ConnectionsDetachable:false
      });
  isRangeStartAndEndInMarkup = (range) ->
    $(range.startContainer).parents().hasClass("markup") && $(range.endContainer).parents().hasClass("markup")
  
  $.contextMenu(
    {
        selector: 'span.markup', 
        callback: (key, options) ->
          switch key
            when "delete" then deleteMarkup(this)
            when "edit" then addComment(this)
        ,
        items: {
          "edit": {name: "Add Comment", icon: "edit"},
          "cut": {name: "Cut", icon: "cut"},
          "copy": {name: "Copy", icon: "copy"},
          "paste": {name: "Paste", icon: "paste"},
          "delete": {name: "Delete", icon: "delete"},
          "sep1": "---------",
          "quit": {name: "Quit", icon: "quit"}
        }
    }
  )
  
  addComment = (elem) ->
    markup_ids = $(elem).attr("data-range-id").split(" ")
    last_markup_id = markup_ids[markup_ids.length - 1]
    $comment = $("<div>", {id : "comment_#{last_markup_id}"}).addClass("comment")
    $textarea = $('<textarea>')
    $comment.append($textarea)
    $("#comments").append($comment)
    $textarea.autosize({append: "\n"});
    $markups = $(":regex(data-range-id, ( )*#{last_markup_id}( )* )")
    $comment.align({top:":regex(data-range-id, ( )*#{last_markup_id}( )* )"})
    $connect = jsPlumb.connect({
      source: $markups[0]
      target: $comment
      anchors: ["TopCenter","TopCenter"]
    })
    $connect.setVisible(false)
    $connect.endpoints[0].setVisible(false)
    $connect.endpoints[1].setVisible(false)
    $comment.qtip({
      content: $('<a>Delete</a>',{href : "#"}).click ->
          History.do(new DeleteCommentMemento(rangy.serializePosition($markups[0],0,$("#doc")[0]),$comment,$connect)) 
          jsPlumb.deleteEndpoint($connect.endpoints[0])
          $comment.remove()        
      position: {
        corner: {
          target: 'bottomRight',
          tooltip: 'topRight'
        }
      }
      hide: { when: 'mouseout', fixed: true }
    })
    $textarea.focus()
    History.do(new AddCommentMemento(rangy.serializePosition($markups[0],0,$("#doc")[0]),$comment,$connect))
  $("#doc").bind 'mouseup', (e) ->
    selection = rangy.getSelection()
    range = selection.getRangeAt(0)
    unless range.collapsed
      selection.removeAllRanges()
      commonAncestor = if range.commonAncestorContainer.nodeType == 3 then range.commonAncestorContainer.parentNode else range.commonAncestorContainer
      # History.do(new MarkupMemento(rangy.serializePosition(commonAncestor,0,$("#doc")[0]),(new XMLSerializer()).serializeToString(commonAncestor)))      
      History.beginCompoundDo()
      markCssApplier.applyToRange(range,markup_id)
      History.endCompoundDo()
      markup_id++
  
  $("#nav").find("a").addClass("unselectable").on( "onselectstart" , ->
        false
  )
          
  $("body").on(
    {
      mouseenter: ->
        markup_ids = $(this).attr("data-range-id").split(" ")
        if markup_ids.length > 0
          last_markup_id = markup_ids[markup_ids.length - 1]
        $(":regex(data-range-id, ( )*#{last_markup_id}( )* )").addClass("markup-selected")
        $("#comment_#{last_markup_id}").addClass("markup-selected")
        if jsPlumb.select({target : "comment_#{last_markup_id}"}).get(0)
          $connect = jsPlumb.select({target : "comment_#{last_markup_id}"}).get(0)
          $connect.setVisible(true)
          $connect.endpoints[0].setVisible(true)
          $connect.endpoints[1].setVisible(true)
             
      mouseleave: ->
        markup_ids = $(this).attr("data-range-id").split(" ")
        if markup_ids.length > 0
          last_markup_id = markup_ids[markup_ids.length - 1]
        $(":regex(data-range-id, ( )*#{last_markup_id}( )* )").removeClass("markup-selected")
        $("#comment_#{last_markup_id}").removeClass("markup-selected")
        if jsPlumb.select({target : "comment_#{last_markup_id}"}).get(0)  
          $connect = jsPlumb.select({target : "comment_#{last_markup_id}"}).get(0)
          $connect.setVisible(false)
          $connect.endpoints[0].setVisible(false)
          $connect.endpoints[1].setVisible(false)
    }
    ".markup"
  )
  
  $("body").on(
    {
      mouseenter: ->
        comment_id = $(this).attr("id")
        markups_id = comment_id.split("_")[1]
        $(":regex(data-range-id, ( )*#{markups_id}( )* )").addClass("markup-selected")
        $(this).addClass("markup-selected")
        $connect = jsPlumb.select({target : "#{comment_id}"}).get(0)
        $connect.setVisible(true)
        $connect.endpoints[0].setVisible(true)
        $connect.endpoints[1].setVisible(true)
        
      mouseleave: ->
        comment_id = $(this).attr("id")
        markups_id = comment_id.split("_")[1]
        $(":regex(data-range-id, ( )*#{markups_id}( )* )").removeClass("markup-selected")
        $(this).removeClass("markup-selected")
        $connect = jsPlumb.select({target : "#{comment_id}"}).get(0)
        $connect.setVisible(false)
        $connect.endpoints[0].setVisible(false)
        $connect.endpoints[1].setVisible(false)
        
    }
    ".comment"
  )
  
  $("body").resize( ->
      jsPlumb.repaintEverything()
  )
  
  deleteMarkup = (markup) ->
    markup_ids = $(markup).attr("data-range-id").split " "
    last_markup_id = markup_ids[markup_ids.length - 1]
    $markupsToDelete = $(":regex(data-range-id, ( )*#{last_markup_id}( )* )")
    commonAncestor = getCommonAncestor($markupsToDelete[0],$markupsToDelete[$markupsToDelete.length - 1])
    History.beginCompoundDo()
    $comment = $("#comment_#{last_markup_id}")
    $connect = jsPlumb.select({target : "comment_#{last_markup_id}"}).get(0)
    History.do(new DeleteCommentMemento(rangy.serializePosition($markupsToDelete[0],0,$("#doc")[0]),$comment,$connect)) 
    History.do(new MarkupMemento(rangy.serializePosition(commonAncestor,0,$("#doc")[0]),(new XMLSerializer()).serializeToString(commonAncestor)))
    $markupsToDelete.each((index) ->
        markup_ids = $(this).attr("data-range-id").split(" ")
        if markup_ids.length <= 1   
          $(this).removeClass("markup").removeAttr("data-range-id")
        else
          last_markup_id_index = markup_ids.indexOf("#{last_markup_id}")
          if last_markup_id_index > -1
            markup_ids.splice(last_markup_id_index,1)
          $(this).attr("data-range-id",markup_ids.join(" "))
    )
    jsPlumb.deleteEndpoint($connect.endpoints[0])
    $comment.remove() 
    History.endCompoundDo()

  getCommonAncestor = (a, b) ->
  
    $parentsa = $(a).parents()
    $parentsb = $(b).parents()
    found = null
    $parentsa.each(->
      thisa = this

      $parentsb.each(->
          if (thisa == this)     
            found = this
            return false
      )
      return false if found
    )
    found

  $("#undo-btn").click ->
      History.undo()
  
    $("#redo-btn").click ->
      History.redo()
    
  class AddCommentMemento
    
    constructor: (@markup,@comment,@connect) -> 
    
    restore: ->
      console.log(@connect.endpoints)
      jsPlumb.deleteEndpoint(@connect.endpoints[0])
      @comment.remove()
      new DeleteCommentMemento(@markup,@comment,@connect)
            
      
  class DeleteCommentMemento
    
    constructor: (@markup,@comment,@connect) ->
    
    restore: ->
      console.log("undo delete comment")
      $comment = @comment
      $markup = @markup
      $("#comments").append($comment)
      last_markup_id = $comment.attr("id").split("_")[1]
      $comment.align({top:":regex(data-range-id, ( )*#{last_markup_id}( )* )"})
      $connect = jsPlumb.connect({
        source: $(rangy.deserializePosition($markup,$("#doc")[0]).node)
        target: $comment
        anchors: ["TopCenter","TopCenter"]
      })
      $comment.qtip({
        content: $('<a>Delete</a>',{href : "#"}).click ->
          History.do(new DeleteCommentMemento($markup,$comment,$connect)) 
          jsPlumb.deleteEndpoint($connect.endpoints[0])
          $comment.remove()
        position: {
          corner: {
            target: 'bottomRight'
            tooltip: 'topRight'
          }
        }
        hide: { when: 'mouseout', fixed: true }
      })
      new AddCommentMemento($markup,$comment,$connect)
      
  class MarkupMemento
    
    constructor: (@node , @xml) ->
    
    restore: ->
      state = new MarkupMemento(@node, (new XMLSerializer()).serializeToString(rangy.deserializePosition(@node,$("#doc")[0]).node) )
      $(rangy.deserializePosition(@node,$("#doc")[0]).node).replaceWith(@xml)
      state

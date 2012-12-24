require 'nokogiri'
require 'fileutils'
require 'execjs'
require 'json'
require 'open-uri'

class DocumentsController < ApplicationController

  PRIVATE_DOCUMENT_DIRECTORY = "app/documents"

  def index
    if current_user
      @documents = Document.all
    end
  end

  def new
    @document = Document.new
  end

  def create
    @document = Document.new(params[:document])
    @document.save
    redirect_to documents_url
  end

  def show
    if current_user
      doc = Document.find(params[:id])
      if doc
        markupHasMaxID = Markup.where(:user_id => current_user.id, :document_id => doc.id).order("mid DESC").limit(1).first()
        @last_markup_id = markupHasMaxID.mid if markupHasMaxID
        @last_markup_id ||= 0
        logger.info "last_markup_id = #{@last_markup_id}"
        session[:opened_doc] = doc
        content = File.read(doc.filepath, :mode => "r")
        parsed_content=Nokogiri::HTML(content)
        @body = parsed_content.css("body").children.to_html.html_safe
        @title = parsed_content.css("title").children[0]
        markups = Markup.where(:user_id => current_user.id, :document_id => doc.id)
        @textRanges = []
        markups.each { |markup| 
            markup.textRanges.each {|textRange| 
              # logger.info textRange.inspect
              textRange.mid = markup.mid 
              textRange.className = markup.className
              # logger.info textRange.className
              # logger.info markup.inspect
              @textRanges.push(textRange)
            }    
        }
        @textRanges.sort! {|x,y|
          x_offset = x.start_position.split(':')[1]
          y_offset = y.start_position.split(':')[1]
          x_hierachy = x.start_position.split(':')[0].split('/').reverse
          y_hierachy = y.start_position.split(':')[0].split('/').reverse
          result = 0
          x_hierachy.each_index { |index|
            if y_hierachy[index] and y_hierachy[index] > x_hierachy[index]
              result = -1
              break
            elsif not y_hierachy[index] or y_hierachy[index] < x_hierachy[index]
              result = 1
              break
            end
          }

          if result == 0
            if x_hierachy.length > y_hierachy.length
              result = 1
            elsif y_hierachy.length > x_hierachy.length
              result = -1
            elsif 
              if x_offset > y_offset
                result = 1
              else y_offset > x_offset
                result = -1
              end
            end
          end
          result
        }
        @textRanges = @textRanges.to_json(:only => [ :id, :start_position, :length ], :methods => [:mid, :className] )
        @comments = markups.map{ |markup| 
          if markup.comment
            markup.comment.mid = markup.mid
            markup.comment.className = markup.className
          end
          markup.comment           
        }.find_all{ |comment| comment }.to_json(:except => [ :markup_id, :created_at, :updated_at ], :methods => [:mid, :className])

        outlineHasMaxID = Outline.where(:user_id => current_user.id, :document_id => doc.id).order("oid DESC").limit(1).first()
        @last_saved_outline_id = outlineHasMaxID.oid if outlineHasMaxID
        @last_saved_outline_id ||= 0
        @outlines = Outline.where(:user_id => current_user.id, :document_id => doc.id).to_json(:except => [:user_id, :document_id, :created_at, :updated_at])
        @users = []
        30.times do 
          @users << User.first
        end
      end
    else
      redirect_to "/auth/facebook"
    end
  end

  def load
    if current_user and params[:user_id]
      doc = session[:opened_doc]
      if doc
        markupHasMaxID = Markup.where(:user_id => params[:user_id], :document_id => doc.id).order("mid DESC").limit(1).first()
        last_markup_id = markupHasMaxID.mid if markupHasMaxID
        last_markup_id ||= 0
        markups = Markup.where(:user_id => params[:user_id], :document_id => doc.id)
        textRanges = []
        markups.each { |markup| 
            markup.textRanges.each {|textRange| 
              textRange.mid = markup.mid 
              textRange.className = markup.className
              textRanges.push(textRange)
            }    
        }
        textRanges.sort! {|x,y|
          x_offset = x.start_position.split(':')[1]
          y_offset = y.start_position.split(':')[1]
          x_hierachy = x.start_position.split(':')[0].split('/').reverse
          y_hierachy = y.start_position.split(':')[0].split('/').reverse
          result = 0
          x_hierachy.each_index { |index|
            if y_hierachy[index] and y_hierachy[index] > x_hierachy[index]
              result = -1
              break
            elsif not y_hierachy[index] or y_hierachy[index] < x_hierachy[index]
              result = 1
              break
            end
          }

          if result == 0
            if x_hierachy.length > y_hierachy.length
              result = 1
            elsif y_hierachy.length > x_hierachy.length
              result = -1
            elsif 
              if x_offset > y_offset
                result = 1
              else y_offset > x_offset
                result = -1
              end
            end
          end
          result
        }
        textRanges = textRanges.to_json(:only => [ :id, :start_position, :length ], :methods => [:mid, :className] )
        comments = markups.map{ |markup| 
          if markup.comment
            markup.comment.mid = markup.mid
            markup.comment.className = markup.className
          end
          markup.comment           
        }.find_all{ |comment| comment }.to_json(:except => [ :markup_id, :created_at, :updated_at ], :methods => [:mid, :className])

        outlineHasMaxID = Outline.where(:user_id => params[:user_id], :document_id => doc.id).order("oid DESC").limit(1).first()
        last_saved_outline_id = outlineHasMaxID.oid if outlineHasMaxID
        last_saved_outline_id ||= 0
        outlines = Outline.where(:user_id => params[:user_id], :document_id => doc.id).to_json(:except => [:user_id, :document_id, :created_at, :updated_at])

        render :json => {:success => true, 
              :last_markup_id => last_markup_id,
              :textRanges => textRanges,
              :comments => comments,
              :last_saved_outline_id => last_saved_outline_id,
              :outlines => outlines
               }
      end
    end
  end


  def save
    if current_user and session[:opened_doc]
      mid_pk_hash = Hash.new  
      text_range_mid_pk_hash = Hash.new
      comment_mid_pk_hash = Hash.new
      outline_oid_pk_hash = Hash.new

      Markup.transaction do
        if params[:markupsToAdd]
          markupsToAdd = params[:markupsToAdd]        
          markupsToAdd.each_value { |markupToAdd|
            markupToAdd[:document] = session[:opened_doc]
            markupToAdd[:user] = current_user
            markup = Markup.new(markupToAdd)
            logger.log markup.errors.full_messages unless markup.save()
            if markup.id
              mid_pk_hash[markup.mid] = markup.id
            end
          }

        end
        
        if params[:markupsToDelete] 
          logger.info params[:markupsToDelete]
          markupsToDelete = params[:markupsToDelete]
          markupsToDelete.each { |mid|
            logger.info Hash[:user_id => current_user.id, :document_id => session[:opened_doc].id, :mid => mid ]
            markupToDelete = Markup.where(:user_id => current_user.id, :document_id => session[:opened_doc].id, :mid => mid).first()
            if markupToDelete
              markupToDelete.destroy()
            end
          }
        end

        if params[:commentsToAdd]
          commentsToAdd = params[:commentsToAdd]
          commentsToAdd.each_value { |commentToAdd|
            if commentToAdd[:id]
              comment = Comment.find_by_id(commentToAdd[:id]) 
            end
            unless comment
              comment = Comment.create()
              commentToAdd[:markup_id] =  mid_pk_hash[Integer(commentToAdd[:mid])]
              unless commentToAdd[:markup_id]
                commentToAdd[:markup_id] = Markup.where(["user_id = ? ANd document_id = ? AND mid = ?",
                    current_user.id, session[:opened_doc].id,
                   Integer(commentToAdd[:mid]) ]).select("id").first.id
              end
              comment_mid_pk_hash[commentToAdd[:mid]] = comment.id
            end
            logger.log comment.errors.full_messages unless comment.update_attributes(commentToAdd.except(:mid))
          }
        end

        if params[:commentsToDelete]
          commentsToDelete = params[:commentsToDelete]
          commentsToDelete.each{ |id|
            comment_to_delete = Comment.find_by_id(id)
            if comment_to_delete
              comment_to_delete.destroy
            end
          }
        end
        
        if params[:outlinesToAdd]
          outlinesToAdd = params[:outlinesToAdd]
          outlinesToAdd.each_value { |outlineToAdd|
            if outlineToAdd[:id] and outline = Outline.find_by_id(outlineToAdd[:id])
              outline.update_attributes(outlineToAdd)
            else
              outlineToAdd[:document] = session[:opened_doc]
              outlineToAdd[:user] = current_user
              outline = Outline.new(outlineToAdd)
              logger.log outline.errors.full_messages unless outline.save()
              outline_oid_pk_hash[outlineToAdd[:oid]] = outline.id
            end
          }
        end

        if params[:outlinesToDelete]
          params[:outlinesToDelete].each{ |outlineToDelete|
            if outline = Outline.find_by_id(outlineToDelete)
              outline.destroy
            end
          }
        end
        
        if params[:textRanges]
          textRanges = params[:textRanges]
          textRanges.each_value { |textRange|
            logger.info textRange
            if textRange[:id]
              textRangeModel = TextRange.find_by_id(textRange[:id])
              unless textRangeModel
                textRangeModel = TextRange.create()
                textRange[:markup_id] =  mid_pk_hash[Integer(textRange[:mid])]
                text_range_mid_pk_hash[textRange[:mid]] = textRangeModel.id 
              end
            else
              textRangeModel = TextRange.create()
              textRange[:markup_id] =  mid_pk_hash[Integer(textRange[:mid])]
              text_range_mid_pk_hash[textRange[:mid]] = textRangeModel.id
            end
            logger.info textRangeModel.errors.full_messages unless textRangeModel.update_attributes(textRange.except(:mid))

          }
        end
      end

      render :json => {:success => true, 
              :text_range_mid_pk_hash => text_range_mid_pk_hash, 
              :comment_mid_pk_hash => comment_mid_pk_hash,
              :outline_oid_pk_hash => outline_oid_pk_hash
               }
    end
  end

  def destroy
    Document.destroy(params[:id])
    redirect_to documents_url
  end

  def load_bible

  end

  private

    def copyDocToUserDir(doc)
      user_private_dir =  File.join(PRIVATE_DOCUMENT_DIRECTORY,current_user.id.to_s)
      user_private_filepath = File.join(user_private_dir,File.basename(doc.filepath))
      FileUtils.mkpath(user_private_dir) unless File.exists?(user_private_dir)
      File.open(user_private_filepath,"wb") { |f| f.write(File.new(doc.filepath).read)} unless File.exists?(user_private_filepath)
      user_private_filepath
    end

end

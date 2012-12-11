require 'nokogiri'
require 'fileutils'
require 'execjs'
require 'json'

class DocumentsController < ApplicationController

  PRIVATE_DOCUMENT_DIRECTORY = "app/documents"

  def index
    @documents = Document.all
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
        user_private_filepath = copyDocToUserDir(doc)
        session[:user_private_filepath] = user_private_filepath
        session[:opened_doc] = doc
        content = File.read(user_private_filepath, :mode => "r")
        parsed_content=Nokogiri::HTML(content)
        @body = parsed_content.css("body").children.to_html.html_safe
        @title = parsed_content.css("title").children[0]
        @last_markup_id
      end
    else
      redirect_to "/auth/facebook"
    end
  end


  def save
    if current_user and session[:user_private_filepath]
      if params[:html]
        content = File.read(session[:user_private_filepath], :mode => "r")
        parsed_content=Nokogiri::HTML(content)
        parsed_content.css("body")[0].inner_html = params[:html]
        File.open(session[:user_private_filepath], "w") { |file| file.write(parsed_content.to_html) }
      end
      
      if params[:markupsToAdd]
        markupsToAdd = params[:markupsToAdd]        
        markupsToAdd.each { |key, markupToAdd|
          markupToAdd[:document] = session[:opened_doc]
          markupToAdd[:user] = current_user
          markup = Markup.new(markupToAdd)
          logger.log markup.errors.full_messages unless markup.save()
        }
      end
      
      if params[:markupsToDelete] 
        logger.info params[:markupsToDelete]
        markupsToDelete = params[:markupsToDelete]
        markupsToDelete.each { |mid|
          logger.info Hash[:user_id => current_user.id, :document_id => session[:opened_doc].id, :mid => mid ]
          markupToDelete = Markup.where(:user_id => current_user.id, :document_id => session[:opened_doc].id, :mid => mid).first()
          markupToDelete.destroy() if markupToDelete
        }
      end
      render :json => {:success => true}
    end
  end

  def destroy
    Document.destroy(params[:id])
    redirect_to documents_url
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

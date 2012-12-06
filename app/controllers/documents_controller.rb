require 'nokogiri'

class DocumentsController < ApplicationController

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
      content = File.read(Document.find(params[:id]).filepath, :mode => "r")
      parsed_content=Nokogiri::HTML(content)
      @body = parsed_content.css("body").children.to_html.html_safe
      @title = parsed_content.css("title").children[0]
    else
      redirect_to "/auth/facebook"
    end
  end

  def destroy
    Document.destroy(params[:id])
    redirect_to documents_url
  end

end

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
    content = File.read(Document.find(params[:id]).filepath, :mode => "r", :encoding => "Big5")
    parsed_content=Nokogiri::HTML(content)
    @body = parsed_content.css("body").children.to_html.html_safe
    @title = parsed_content.css("head title").children[0].to_str
  end

  def destroy
    Document.destroy(params[:id])
    redirect_to documents_url
  end

end

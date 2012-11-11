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
    render :text => content
  end
  
  def destroy
    Document.destroy(params[:id])
    redirect_to documents_url
  end
  
end

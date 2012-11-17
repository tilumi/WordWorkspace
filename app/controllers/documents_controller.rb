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

    js_elem = Nokogiri::XML::Node.new "script", parsed_content
    js_elem["src"] = "/assets/jquery.js?body=1"
    js_elem["type"] = "text/javascript"
    parsed_content.css("head").first << js_elem

    js_elem = Nokogiri::XML::Node.new "script", parsed_content
    js_elem["src"] = "/assets/jquery_ujs.js?body=1"
    js_elem["type"] = "text/javascript"
    parsed_content.css("head").first << js_elem

    js_elem = Nokogiri::XML::Node.new "script", parsed_content
    js_elem["src"] = "/assets/rangy-core.js?body=1"
    js_elem["type"] = "text/javascript"
    parsed_content.css("head").first << js_elem

    js_elem = Nokogiri::XML::Node.new "script", parsed_content
    js_elem["src"] = "/assets/document.js?body=1"
    js_elem["type"] = "text/javascript"
    parsed_content.css("head").first << js_elem

    css_elem = Nokogiri::XML::Node.new "link", parsed_content
    css_elem["href"] = "/assets/document.css?body=1"
    css_elem["media"] = "all"
    css_elem["rel"] = "stylesheet"
    css_elem["type"] = "text/css"
    parsed_content.css("head").first << css_elem

    render :text => parsed_content.to_html
  end

  def destroy
    Document.destroy(params[:id])
    redirect_to documents_url
  end

end

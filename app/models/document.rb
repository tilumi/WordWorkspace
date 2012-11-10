class Document < ActiveRecord::Base
  attr_accessible :filepath, :title, :uploaded_file
  attr_writer :uploaded_file

  before_create :store_doc
  after_destroy :delete_html

  protected
  def store_doc
    name = @uploaded_file.original_filename
    directory = "public/documents"
    path = File.join(directory, name)
    File.open(path,"wb") { |f| f.write(@uploaded_file.read)}
    doc_to_html(path)
  end

  def doc_to_html(path)

    if path =~ /\.doc$|\.docx$/
      t = Thread.new do
        word = WIN32OLE.new('Word.Application')
        word.visible = false
        word.documents.open Rails.root.join(path).to_s.gsub("/","\\")
        word.activedocument.saveas( Rails.root.join(path.gsub(/\.doc$|\.docx$/,".html")).to_s.gsub("/","\\"), 10)
        word.quit
      end
    t.join
    File.delete(path)
    self.filepath = path.gsub(/\.doc$|\.docx$/,".html")
    end
  end

  def delete_html
    File.delete(filepath) if filepath and File.exist?(filepath)
  end
  
end

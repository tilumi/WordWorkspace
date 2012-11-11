class Document < ActiveRecord::Base
  DOCUMENT_DIRECTORY = "public/documents"
  attr_accessible :filepath, :title, :uploaded_file, :original_filename
  attr_writer :uploaded_file

  before_create :store_doc
  after_destroy :delete_associate_files

  protected
  
  def my_logger
    @@my_logger ||= Logger.new("#{Rails.root}/log/my.log")
  end
  
  def new_filename(original_filename)
    "#{DateTime.now.strftime("%Y%m%d%H%M%S")}#{original_filename[original_filename.rindex(".")..-1]}"
  end
  
  def store_doc
    self.original_filename = @uploaded_file.original_filename
    path = File.join(DOCUMENT_DIRECTORY, new_filename(self.original_filename))
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
    self.filepath = path.gsub(/\.doc$|\.docx$/,".html")
    end
  end

  def delete_associate_files
    Dir.entries(DOCUMENT_DIRECTORY).each do |filename|
      File.delete(File.join(DOCUMENT_DIRECTORY,filename)) if filename =~ /^#{filepath[filepath.rindex("/")+1..filepath.rindex(".")-1]}/
     end
  end
  
end

require "fileutils"
require "open4"
class Document < ActiveRecord::Base
  DOCUMENT_DIRECTORY = "public/documents"
  attr_accessible :filepath, :title, :uploaded_file, :original_filename
  attr_writer :uploaded_file

  has_many :markups, :dependent => :delete_all

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
    Dir.mkdir(DOCUMENT_DIRECTORY) unless File.exists?(DOCUMENT_DIRECTORY)
    self.original_filename = @uploaded_file.original_filename
    path = File.join(DOCUMENT_DIRECTORY, self.original_filename)
    File.open(path,"wb") { |f| f.write(@uploaded_file.read)}
    doc_to_html(path)
  end

  def doc_to_html(path)

    if path =~ /\.doc$|\.docx$/
      Open4::open4("sh") do |pid, stdin,stdout,stderr|
        logger.info(path)
        logger.info("#{path[0..path.rindex(".")]}html")
        stdin.puts "java -jar /Users/MingFu/Downloads/jodconverter/lib/jodconverter #{path} #{path[0..path.rindex(".")]}html"
        stdin.close
        puts "#{stdout.read.strip}"
        puts "#{stderr.read.strip}"
      end

      filename = path[path.rindex("/")+1..path.rindex(".")-1]
      self.filepath = path.gsub(/\.doc$|\.docx$/,".html")
    end
  end

  def delete_associate_files
    Dir.entries(DOCUMENT_DIRECTORY).each do |filename|
      FileUtils.rm_r(File.join(DOCUMENT_DIRECTORY,filename)) if filename =~ /^#{filepath[filepath.rindex("/")+1..filepath.rindex(".")-1]}/
     end
  end

end

class Video < ActiveRecord::Base
  attr_accessible :document_id, :path
  belongs_to :document
end

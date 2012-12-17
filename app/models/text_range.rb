class TextRange < ActiveRecord::Base
  # @mid = 0
  # @className = ''
  attr_accessor :mid, :className
  attr_accessible :length, :markup_id, :start_position
  belongs_to :markup

 
end

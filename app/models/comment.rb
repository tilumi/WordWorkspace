class Comment < ActiveRecord::Base
  attr_accessor :mid, :className
  attr_accessible :content, :x, :y, :width, :height, :markup_id
  belongs_to :markup
 end

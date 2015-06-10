class Kumamarkdownrenderer < Redcarpet::Render::HTML
  def block_code(code, language)
    formatter = Rouge::Formatters::HTML.new(wrap: false)
    if language and language.include?('example')
      if language.include?('js')
        lexer = Rouge::Lexer.find('js')
        # first actually insert the code in the docs so that it will run and make our example work.
        '<script>' + code + '</script> <div class="codeBlock jsExample"><div class="highlight"><pre>' + formatter.format(lexer.lex(code)) + '</pre></div></div>'
      elsif language.include?('none')
        # with `none_example`, just the rendered html gets rendered
        lexer = Rouge::Lexer.find('none')
        render_html(code, language)
      else
        random_number = SecureRandom.hex(3)
        lexer = Rouge::Lexer.find(get_lexer(language))
        '<div class="exampleContainer"><div class="codeExample">' + '<div class="exampleOutput">' + render_html(code, language) + '</div>' + '<div class="exampleCode"><button class="js-styleguide-toggle-btn toggle-button toggle-button-styleguide" data-duration="300" data-target="#' + random_number + '"><span>Show markup</span> <i class="icon icon--arrow-up"></i></button><div id="' + random_number + '" class="toggle-item toggle-item--styleguide"><div class="toggle-item__content"><div class="codeBlock"><div class="highlight"><pre>' + formatter.format(lexer.lex(code)) + '</pre></div></div></div></div></div></div></div>'
      end
    else
      lexer = Rouge::Lexer.find_fancy('guess', code)
      '<div class="codeBlock"><div class="highlight"><pre>' + formatter.format(lexer.lex(code)) + '</pre></div></div>'
    end
  end

  private
  def render_html(code, language)
    case language
      when 'haml_example'
        safe_require('haml', language)
        return Haml::Engine.new(code.strip).render(template_rendering_scope, {})
      else
        code
    end
  end

  def template_rendering_scope
    Object.new
  end

  def get_lexer(language)
    case language
      when 'haml_example'
        'haml'
      else
        'html'
    end
  end

  def safe_require(templating_library, language)
    begin
      require templating_library
    rescue LoadError
      raise "#{templating_library} must be present for you to use #{language}"
    end
  end
end

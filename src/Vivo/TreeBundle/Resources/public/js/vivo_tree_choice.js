$(function(){
    var root_choices =  $('.vivo-tree-choice[data-level="0"]');
    for(var i=0; i< root_choices.length; i++){
        // Start recursion from base nodes
        processNode(root_choices[i]);
    }

    $('.vivo-tree-choice span.expand').click(function(){
        var context = $(this).parent().siblings('input');
        handleClickEvent(context, false);
    });

    $('.vivo-tree-choice input').change(function(){
        handleClickEvent(this, true);
    });

    /**
     *
     * @param context
     * @param checked_event - whether this was triggered by a checkbox changing
     */
    function handleClickEvent(context, checked_event){
        var choice = $(context).parents(".vivo-tree-choice");
        if((context.checked && checked_event) || (!checked_event && (choice.attr("data-open")=='false'))){
            // If a check event, then ensure parents are checked
            $(choice).attr('data-open', true);

            if(checked_event){
                var prevChoices = choice.prevAll(".vivo-tree-choice[data-level]");
                var nextLevel = choice.attr('data-level')-1;
                // Assuming it's not a root node already
                if(nextLevel>=0){
                    for(var i=0; i<prevChoices.length; i++){
                        var thisChoice = prevChoices[i];
                        var thisLevel = $(thisChoice).attr("data-level");
                        // If this is the root level, check this first one then quit (this is the base parent)
                        if(thisLevel == 0){
                            $(thisChoice).find("input").prop('checked', true);
                            break;
                        }else if(thisLevel == nextLevel){
                            nextLevel--;
                            $(thisChoice).find("input").prop('checked', true);
                        }
                    }
                }
            }

            // check for children (down)
            var nextChoices = choice.nextAll(".vivo-tree-choice[data-level]");
            for(var i=0; i<nextChoices.length; i++){
                var thisChoice = nextChoices[i];
                if($(thisChoice).attr("data-level")<=choice.attr("data-level")){
                    break;
                }else if($(thisChoice).attr("data-level") <= (1+parseInt(choice.attr("data-level")))){
                    $(thisChoice).show();
                }
            }
            $(choice).find("span.expand").text("[-]");

        }else{
            // If not a checked event, then collapse but don't deselect

            var nextChoices = choice.nextAll(".vivo-tree-choice[data-level]");
            if(!checked_event){
                $(choice).attr('data-open', false);
                $(choice).find("span.expand").text("[+]");
            }

            for(var i=0; i<nextChoices.length; i++){
                var thisChoice = nextChoices[i];
                if($(thisChoice).attr("data-level") <= choice.attr("data-level")){
                    break;
                }else{
                    if(!checked_event){
                        $(thisChoice).find("span.expand").text("[+]");
                        $(thisChoice).attr('data-open', false);
                        $(thisChoice).hide();
                    }else{
                        $(thisChoice).find("input").prop('checked', false);
                    }
                }
            }
        }
    }

    function processNode(element){
        var this_choice = element;
        var choice_level = $(this_choice).attr("data-level");
        var checkbox =  $(this_choice).find("input").first()[0];

        var label = $(this_choice).find("label");
        label.prop("for", checkbox.id);

        var children = [];
        if(checkbox.checked){
            // Checked, process first level children
            var nextLevel = parseInt($(this_choice).attr("data-level"))+1;
            var next_results = $(this_choice).nextAll('.vivo-tree-choice[data-level]');
            $(this_choice).attr('data-open', true);

            for(var j=0; j<next_results.length; j++){
                var this_child = next_results[j];
                var child_level = $(this_child).attr("data-level");
                // If the level is equal or less than, then it's a sibling/parent
                if(child_level <= choice_level){
                    // Not a child
                    break;
                }else{
                    // This is a child
                    children.push(this_child);
                }
            }
            if(children.length){
                var label = $(this_choice).find("label").html();
                $(this_choice).find("label").before("<span class='expand'>[-]</span>");
            }
            $.each(children, function(){
                // Recursively process each node
                $(this).attr('data-open', true);

                processNode(this);
            });
            return true;

        }else{
            // Process each child, if any are open then don't close this one
            var next_results = $(this_choice).nextAll('.vivo-tree-choice[data-level]');
            $(this_choice).attr('data-open', false);

            for(var j=0; j<next_results.length; j++){
                var this_child = next_results[j];
                var child_level = $(this_child).attr("data-level");
                if(child_level <= choice_level){
                    // Not a child
                    break;
                }else{
                    children.push(this_child);
                }
            }
            var anyOpen = false;
            $.each(children, function(){
                anyOpen = anyOpen || processNode(this);
            });

            if(anyOpen == false){
                // Should be closed
                $.each(children, function(){
                    $(this).attr('data-open', false);
                    $(this).hide();
                });

                if(children.length){
                    var label = $(this_choice).find("label").html();
                    $(this_choice).find("label").before("<span class='expand'>[+]</span>");
                }
                return false;
            }else{
                // Should be open
                if(children.length){
                    var label = $(this_choice).find("label").html();
                    $(this_choice).find("label").before("<span class='expand'>[-]</span>");
                }
                $(checkbox).prop("checked", true);
                return true;
            }
        }
    }
});
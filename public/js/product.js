$('#add-image').click(function()
{
    const index = +$('#widget-counter').val();
    const tmpl = $('#product_images').data('prototype').replace(/__name__/g, index);
    
    $('#product_images').append(tmpl);

    $('#widget-counter').val(index+1);

    handleDeleteButtons();
});

function handleDeleteButtons()
{
    $('button[data-action="delete"]').click(function()
    {
        const target = this.dataset.target;
        
        $(target).remove();
    })
}

function updateCounter()
{
    const count = +$('#product_images div.form-group').length;
    
    $('#widget-counter').val(count);
}
updateCounter();
handleDeleteButtons();
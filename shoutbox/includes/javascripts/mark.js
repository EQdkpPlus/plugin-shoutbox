// -------------------------------------------------------------------
// Mark/Unmark checkboxes
// by Aderyn
// -------------------------------------------------------------------

function markAllSBEntries(container_id)
{
  var rows = document.getElementById(container_id).getElementsByTagName('tr');
  var checkbox;

  for (var i = 0; i < rows.length; i++) 
  {
    checkboxes = rows[i].getElementsByTagName('input');
    for (var j = 0; j < checkboxes.length; j++) 
    {
      if (checkboxes[j] && checkboxes[j].type == 'checkbox')
      {
        if (checkboxes[j].disabled == false) 
        {
          checkboxes[j].checked = true;
        }
      }
    }
  }

  return true;
}

function unmarkAllSBEntries(container_id)
{
  var rows = document.getElementById(container_id).getElementsByTagName('tr');
  var checkbox;

  for (var i = 0; i < rows.length; i++) 
  {
    checkboxes = rows[i].getElementsByTagName('input');
    for (var j = 0; j < checkboxes.length; j++) 
    {
      if (checkboxes[j] && checkboxes[j].type == 'checkbox')
      {
        if (checkboxes[j].disabled == false) 
        {
          checkboxes[j].checked = false;
        }
      }
    }
  }

  return true;
}

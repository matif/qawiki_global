function rowEffect(object, current_class, new_class) {
	if (object.className == current_class) object.className = new_class;
	return;
} 

function fieldEffect(object, assign_class) {
	object.className = assign_class;
	return;
}
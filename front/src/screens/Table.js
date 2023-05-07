import React, { useEffect, useRef, useState } from 'react';
import 'devextreme/dist/css/dx.common.css';
import 'devextreme/dist/css/dx.light.css';
import '../App.css'
import {
  TreeList,
  Selection,
  Editing,
  RowDragging,
  Paging,
  Form,
  Item,
  RequiredRule,
  EmailRule
} from 'devextreme-react/tree-list';
import { getEmployer, storeEmployer, updateEmployer } from '../service/service';
import { destroyEmployer } from '../service/service';


function EmployeeTree() {
  const [isEditing, setIsEditing] = useState(false)
  const [currentEmployees, setCurrentEmployees] = useState([]);
  const treeListRef = useRef(null);
  useEffect(() => {
    getEmployer('').then((res) => {
      setCurrentEmployees(res.data)
    })
  }, [])

  const getEmployees = async (e) => {
    try {
      const employees = await getEmployer(e)
      let x = currentEmployees.filter((elm) => elm.parentId !== e)
      setCurrentEmployees([...x, ...employees.data])
    } catch (error) {
      console.log(error);
    }
  }



  const handleRowRemoved = (e) => {
    destroyEmployer(e.key)
  }

  const handleFormSubmit = async (newData) => {  
    if (newData.changes[0]?.type === "insert") {
      newData.cancel = true
      try {
        const data = newData.changes[0]?.data
        let job_title = data.parentId === null ? 'employer' : "employee"
        data.job_title = job_title
        const newItem = await storeEmployer(data)
        setCurrentEmployees([...currentEmployees, newItem.data])
        treeListRef.current.instance.cancelEditData()
      } catch (error) {
        alert(error.response?.data.message)
      }
    } else if (newData.changes[0].type === "update") {
      newData.cancel = true
      try {
        const id = newData.changes[0].key
        const data = newData.changes[0]?.data
        const newItem = await updateEmployer({ data, id })
        let newDataaaa = currentEmployees.map((item) => item.id === id ? newItem.data : item)
        setCurrentEmployees(newDataaaa)
        treeListRef.current.instance.cancelEditData()
      } catch (error) {
        alert(error.response.data.message)
      }
    }
  }

  const columns = [
    { dataField: 'id', caption: 'ID' },
    { dataField: 'name', caption: 'Name' },
    { dataField: 'surname', caption: 'Surname' },
    { dataField: 'job_title', caption: 'Job Title' },
    { dataField: 'email', caption: 'Email' },
    { dataField: 'phone', caption: 'Phone' },
    { dataField: 'note', caption: 'Notes' },
  ];

  const onDragChange = (e) => {
    let visibleRows = e.component.getVisibleRows(),
      sourceNode = e.component.getNodeByKey(e.itemData.id),
      targetNode = visibleRows[e.toIndex].node;

    while (targetNode && targetNode.data) {

      if (targetNode.data.id === sourceNode.data.id) {

        e.cancel = true;

        break;
      }
      targetNode = targetNode.parent;
    }
  }

  const onReorder = (e) => {
    let visibleRows = e.component.getVisibleRows(),
      sourceData = e.itemData,
      targetData = visibleRows[e.toIndex].data,
      employeesReordered = currentEmployees,
      sourceIndex = employeesReordered.indexOf(sourceData),
      targetIndex = employeesReordered.indexOf(targetData);

    if (e.dropInsideItem) {

      sourceData = { ...sourceData, parentId: targetData.id };
      employeesReordered = [...employeesReordered.slice(0, sourceIndex), sourceData, ...employeesReordered.slice(sourceIndex + 1)];

    } else {
      if (sourceData.parentId !== targetData.parentId) {
        sourceData = { ...sourceData, parentId: targetData.parentId };
        if (e.toIndex > e.fromIndex) {
          targetIndex++;
        }
      }
      employeesReordered = [...employeesReordered.slice(0, sourceIndex), ...employeesReordered.slice(sourceIndex + 1)];
      employeesReordered = [...employeesReordered.slice(0, targetIndex), sourceData, ...employeesReordered.slice(targetIndex)];

    }
    sourceData.job_title = 'employee'
    updateEmployer({ id: sourceData.id, data: sourceData })
    setCurrentEmployees(employeesReordered);
  }

  return (
    <div className="App">
      <TreeList
        ref={treeListRef}
        onRowClick={(e) => {
          e.isExpanded && getEmployees(e.data.id)
        }}
        onRowRemoved={(e) => handleRowRemoved(e)}
        onEditingStart={() => setIsEditing(true)}
        onEditCanceling={() => setIsEditing(false)}
        hasItemsExpr={() => true}
        columns={columns}
        id="treeList"
        dataSource={currentEmployees}
        rootValue={null}
        keyExpr="id"
        parentIdExpr="parentId"
        allowColumnReordering={true}
        allowColumnResizing={true}
        onSaving={handleFormSubmit}
      >
        <Editing
          useIcons={true}
          mode="popup"
          allowUpdating={true}
          allowDeleting={true}
          allowAdding={true}
          texts={{
            confirmDeleteMessage: 'Note that subemployees will be deleted also.',
          }}
        >
          <Form>
            <Item itemType="group" colCount={2} colSpan={2}>
              <Item dataField="name">
                <RequiredRule message="Name is required" />
              </Item>
              <Item dataField="surname">
                <RequiredRule message="Surname is required" />
              </Item>
              <Item dataField="email" disabled={isEditing}>
                <RequiredRule message="Email is required" />
                <EmailRule message="Invalid email format" />
              </Item>
              <Item dataField="phone" >
                <RequiredRule message="Email is required" />
              </Item>
              <Item dataField="note" />
            </Item>
          </Form>
        </Editing>
        <Selection mode="single" />

        <RowDragging
          onDragChange={onDragChange}
          onReorder={onReorder}
          allowDropInsideItem={true}
          allowReordering={true}
          showDragIcons={true}
        />

        <Paging
          enabled={true}
          defaultPageSize={10}
        />

      </TreeList>
    </div>
  );
}

export default EmployeeTree;

